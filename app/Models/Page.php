<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;


class Page extends Model
{
    protected $fillable = ['key', 'description'];
    protected $pageModel = null;
    protected $modelFields = [];
    protected $listNames = [];

    protected $page = null;
    protected $sectionKey = null;

    public function __construct()
    {
        Log::debug(__METHOD__ . ' called');
    }
    /**
     * Retrieve a Page configuration with all fields to be displayed for a given page context.
     *
     * @param  string  $pageName  Logical page identifier (e.g. 'display', 'pdf', 'edit', 'search')
     * @param  string  $sectionKey  section identifier ('main')
     * @param  array   forceAddField required value format : ['field_key_name' => string $keyName,
     *                                                        'sort_order' => int $sortOrder]
     * @return Page
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function setPageModelFor(string $pageName, string $sectionKey, $forceUuid= false, array|bool $forceAddField = false): static
    {
        Log::debug(__METHOD__ . ' called.');
        $pageModel = Page::where('key', $pageName)
            ->with(['pageFields' => fn($q) => 
                $q->where('section_key', $sectionKey)
                ->where('is_visible', 1)
                ->orderBy('sort_order','asc')
                ->with(['field:id,key,data_type,storage_type,unit,storage_target'])->select('page_key','section_key','sort_order','field_id')
            ])
            ->firstOrFail();

        if ($forceAddField !== false) {
                $pageModel->pageFields->push(
                    self::makeRuntimePageField(
                        $pageName,
                        $sectionKey,
                        new Field()->where('key', (string) $forceAddField['field_key_name'])->firstOrFail(),
                        (int) $forceAddField['sort_order']
                    )
                );
        }
    
        $this->page = $pageName;
        $this->sectionKey = $sectionKey;
        $this->forceUuid = $forceUuid;

        $this->pageModel = $pageModel;
        
        $this->setModelFields();
        return $this;
    }       

    /**
     * pageField relation
     */
    public function pageFields()
    {
        return $this->hasMany(PageField::class, 'page_key', 'key')
                    ->with('field'); 
                    //->orderBy('sort_order','desc');          
    }

    protected static function makeRuntimePageField(
        string $pageKey,
        string $sectionKey,
        Field $field,
        int $sortOrder
        ): PageField {
            $pageField = new PageField([
                'page_key'    => $pageKey,
                'section_key' => $sectionKey,
                'is_visible'  => 1,
                'sort_order'  => $sortOrder,
            ]);

            // Relation Field injectée manuellement (pas de field_id persisté)
            $pageField->setRelation('field', $field);

            return $pageField;
    }

    /*
     * return a list of formated cave data from their respective display page
     * 
     */
    protected function setModelFields(): static
    {
        Log::debug(__METHOD__ . ' called.');
        
        $currentmodelFields = [];
        foreach($this->pageModel->pageFields as $pf => $value){
            $currentmodelFields[ $value['field']['key'] ] = [
                'i18n_label' => __('varcave.table_cave.' . $value['field']['key'] ),
                'data_type'  => $value['field']['data_type'],
                'unit'       => $value['field']['unit'],
                'storage_type' => $value['field']['storage_type'],
                'storage_target' => $value['field']['storage_target'],
            ];

            
            if($value['field']['storage_type'] === 'list'){
                $this->listNames[] = $value['field']['key'];
                $listValues = [];

                $listValues = ListValue::whereIn('list_name', [$value['field']['storage_target']])->get();
                
                $lists = array();
                foreach( $listValues->toArray() as $listItem){
                    $lists[ $listItem['value'] ] = __( $listItem['i18n_key'] );
                }
               $currentmodelFields[ $value['field']['key'] ]['list_values'] = $lists;
            }

            if($value['field']['storage_type'] === 'relation'){

                $rel = explode('.', $value['field']['storage_target']) ;
                $table = trim($rel[0]);
                $target = trim($rel[1]);
                
                if (count($rel) !== 2 || empty($table) || empty($target)) {
                    throw new \InvalidArgumentException(
                        'Invalid storage_target [' . $value['field']['storage_target'] . '] Expected format: Model.field'
                    );
                }
                
                $modelData = resolve('App\Models\\'.$table)::all()->select(['id', $target]);

                $lists = array();
                foreach( $modelData as $listItem){
                    $lists[$listItem['id']] = $listItem[$target];
                }
               $currentmodelFields[ $value['field']['key'] ]['list_values'] = $lists;
            }
        }
        
        //add Uuid 
        if($this->forceUuid) {
            $currentmodelFields['uuid'] = [
                'i18n_label' => 'uuid',
                'data_type' => 'uuid',
                'unit' => null,
                'storage_type' => null,
                'storage_target' => 'cave.uuid',
            ];
        }

        $this->modelFields = $currentmodelFields;
        return $this;
    }

    public function getModelFields(){
        return $this->modelFields;
    }

    public function getListNames(){
        return $this->ListNames;
    }

    
}

