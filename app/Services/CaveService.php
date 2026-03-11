<?php

namespace App\Services;

use App\Models\Cave;
use App\Models\User;
use App\Models\Page;
use Illuminate\Http\Request;
use App\Models\Field;
use App\Models\Setting;
use App\Models\CaveFile;
use App\Models\ListValue;
use Illuminate\Support\Str;
use App\Models\CaveCoordinates;
use App\ViewModels\CaveViewModel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;
use Illuminate\Database\Eloquent\Collection;


/**
 * CaveService  generate cave object to user presentation
 * 
 * Generated cave objet to be used in view and more.
 * Object created will be like 
 *   (object)CaveServiceObject{
 *   attributes => [
 *              'name' => [
 *                  'i18n_label' => 'i18n name value',
 *                  'value'      => 'cave name', //formated value by view model
 *              ],
 *              'mylist' => [
 *                  'i18n_label' => 'this is a list',
 *                  'value'      => 'value1', //from list_values table
 *                  'list_values' => ['value1', 'value2', 'value3'],
 *              ],
 *   ],
 *   'coordinates' => [
 *      'entrance' =>
 *              [
 *               'x'    => (float) $c->x,
 *               'y'    => (float) $c->y,
 *               'lon' => (float) $c->x,
 *               'lat'  => (float) $c->y,
 *               'z'    => (float) $c->z,
 *              ],
 *              //more entrance/coords if any
 *      'near_caves' =>[
 *              [
 *               'x'    => (float) $c->x,
 *               'y'    => (float) $c->y,
 *               'lon' => (float) $c->x,
 *               'lat'  => (float) $c->y,
 *               'z'    => (float) $c->z,
 *              ],
 *              //others near caves if any
 *      ]
 *   ],
 *   'files' => [
 *          'documents' => [
 *              [
 *                  'path' => '/path/to/file',
 *                  'name' => 'file name',
 *                  'mime_type' => 'image/jpeg', //exemple
 *              ],
 *              //other documents if any
 *          ],
 *          //other files
 *    ],
 *                  
 * 
 *   }
 */
class CaveService
{
    public const ADD_COORDS             = 0x1;
    public const ADD_NEAR_CAVES         = 0x2;
    public const ADD_FILES              = 0x4;
    public const ADD_CHANGELOG          = 0x6;
    //public const ADD_CAVEMAPS_ONLY      = 0x7; // to be implemented
    public const ADD_ALL        =     self::ADD_COORDS
                                    | self::ADD_NEAR_CAVES
                                    | self::ADD_FILES
                                    | self::ADD_CHANGELOG;


    public const OUTPUT_ARRAY = 0;
    public const OUTPUT_STD_OBJ = 1;
    public const OUTPUT_DATATABLES = 2;

    //private string $outputType = self::OUTPUT_ARRAY;

    private bool $renderForView = false;
    private Cave $cave ;
    private $user;
    private Page $page ;
    private Collection $fields;
    private $outputRaw = null;
    private $caveViewReadyData = null;
    private int $OPTIONS;

    private const separatorOptions = [
        'bibliography' => '/',
        'default'   => ',',
    ];

    public function __construct(Cave $cave, ?User $user, int $OPTIONS = 0)
    {
        Log::debug(__METHOD__ . ' called.',['OPTIONS' => $OPTIONS ]);
        //$this->page = new page($pageOptions[0], $pageOptions[1], $pageOptions[2],  $pageOptions[03]);
        $this->cave = $cave;
        $this->user = $user;
        $this->fields = Field::all();
        $this->outputRaw = array();
        $this->OPTIONS = $OPTIONS;
        $this->build();
    }

    private function build(): static 
    {
        Log::debug(__METHOD__ . ' called');
        if(empty($this->cave->uuid)){
            Log::error('cave objet is invalid:', ['cave' => print_r($this->cave, true)]);
            throw new \InvalidArgumentException('Invalid Cave object.');
        }
        
        $allowedKeys = $this->fields->pluck('key')->toArray();
        $this->outputRaw['attributes'] = $this->cave->only($allowedKeys);
        //return more data to authenticated users
        if (Gate::allows('showAllCaveDetails', $this->cave) ) {
            //add ccoordinates to results
            if($this->OPTIONS & self::ADD_COORDS)
            {
                $this->outputRaw['coordinates'] = array(); 
                $caveCoordinates = CaveCoordinates::get($this->cave->uuid, $this->user);

                $this->outputRaw['coordinates']['near_caves'] = null;
                if($this->OPTIONS & self::ADD_NEAR_CAVES)
                {
                    //Search near caves, if this cave have at least 1 set of coordinates defined !
                    if ($caveCoordinates->first()['x'] != 0) { 
                        $nearCaves = CaveCoordinates::findNearCaves($caveCoordinates, Setting::get('near_caves_max_radius'), Setting::get('near_caves_max_number'), $this->cave->id);
                        $this->outputRaw['coordinates']['near_caves'] = $nearCaves->toArray() ;
                    }
                }
                $this->outputRaw['coordinates']['entrance'] = $caveCoordinates->toArray();
            }

            if($this->OPTIONS & self::ADD_FILES){
                //add files to results
                $allFiles = CaveFile::get($this->cave, '*')->toArray();
                $this->outputRaw['caveFiles'] = array();
                foreach($allFiles as $key => $file){
                    $this->outputRaw['caveFiles'][ $file['file_type'] ][] =  [
                        'file_path' => $file['file_path'],
                        'file_note' => $file['file_note'],
                        'created_at' => $file['created_at'],
                    ];
                }
            }
        }
        //add changelog
        if($this->OPTIONS & self::ADD_CHANGELOG){
            $this->outputRaw['changelogs'] = $this->cave->load('changeLog')->changeLog->toarray();
        }

        return $this;
    }

    public function renderForPage(Page $page)
    {
        Log::debug(__METHOD__ . ' called.');
        Log::debug(' list of cave fields:', ['fields:' => print_r($page->getModelFields(), true)]);
        $this->render($page->getModelFields());
        return [
            'attributes' => [
                'data' => $this->caveViewReadyData, 
                'model' => $page->getModelFields(),
            ],
            'coordinates' => $this->outputRaw['coordinates'] ?? null,
            'caveFiles' => $this->outputRaw['caveFiles'] ?? null,
            'changelogs' => $this->outputRaw['changelogs'] ?? null,
        ];
    }

    private function render(array $pageModel, int $outputFormat = 0):void
    {
        Log::debug(__METHOD__ . ' called.');
        //isolate on required fields
        $caveData = array_intersect_key(
            $this->outputRaw['attributes'],
            $pageModel
        );

        $this->caveViewReadyData = array();
        foreach ($caveData as $key => $value)
        {
            $list_values = null;
            if($pageModel[$key]['storage_type'] === 'list') {
                $listValues = ListValue::whereIn('list_name', [ $pageModel[$key]['storage_target'] ])->get()->toArray();
                $list = array();
                foreach( $listValues as $listItem){
                    $list[ $listItem['value'] ] = __($listItem['i18n_key']);
                    //$list[ $listItem['value'] ] = $listItem['value'];
                }
                $list_values = $list;
            } 

            
            $this->caveViewReadyData[$key] = $this->formatValue($value, $key, $pageModel[$key], $list_values);

            if ($list_values === null) {
                //unset($this->caveViewReadyData[$key]['list_values']);
            }
        }
        
    }

    public static function formatValue(mixed $value, string $key, array $fieldDef, $listValues): mixed
    {
        Log::debug(__METHOD__ . ' called.');
        Log::debug('  format data: ' . $key . ' as: ' . $fieldDef['data_type']);
        
        //empty val
        if ($value === null || $value === '') {
            if($fieldDef['data_type'] === 'delimitedArray'){ //return empty array to prevent errors on view
                return [];
            }
            return '---';
        }

        //all other type of data
        return match ($fieldDef['data_type']) {
            'bool' => $value
                ? __('varcave.general.yes')
                : __('varcave.general.no'),
            'timestamp' => date('d/m/Y', $value),
            'date' => $value?->format('d/m/Y'),
            'string' => (string) trim($value . ' ' . $fieldDef['unit']),
            'number' => trim((int) $value . ' ' . $fieldDef['unit']),
            'delimitedArray' => explode(self::separatorOptions[ Str::lower($key)] ??  self::separatorOptions['default'], $value  ),
            default => trim($value),
        };
    }

    public function rebuild(Cave $cave):static {
        $this->outputRaw = array();
        $this->caveViewReadyData = null; 
        $this->cave = $cave;
        $this->build();
        return $this;
    }

    //deprecated ?
    /*public function setFilesExport(bool $flag):static {
        $this->filesExport = $flag;
        return $this;
    }*/

    public function getViewReadyData(int $outputFormat = self::OUTPUT_ARRAY){
        switch($outputFormat){
            case (self::OUTPUT_DATATABLES):
                //dd($this->caveViewReadyData);
                return $this->caveViewReadyData;
                break;

            case (self::OUTPUT_STD_OBJ):
                return (object)$this->caveViewReadyData;
                break;
            
            case (self::OUTPUT_ARRAY):
            default:
                return $this->caveViewReadyData;
        }
    }
}