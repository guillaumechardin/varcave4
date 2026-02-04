<?php
namespace App\ViewModels;

use LogicException;
use App\Models\Cave;
use App\Models\Page;
use App\Models\Field;
use App\Models\ListValue;
use Illuminate\Support\Str;

class CaveViewModel
{
    public const AS_ARRAY = 0;
    public const AS_STD_OBJ = 1;
    public const AS_DATATABLES = 2;

    

    protected array $fields;
    protected array $listValues = [];
    protected Page $page;

    private $separatorOptions = [
        'bibliography' => '/',
        'default'   => ',',
    ];

    public function __construct(Page $page)
    {
        $this->page = $page;
    }

    public function render(Cave $cave, int $outpoutFormat = 0): object|array
    {
        $output = $this->page->getModelFields();
        foreach ($output as $key => &$field){
            if(isset($field['list_values'])) {
                foreach($field['list_values'] as $listKey => $listVal){
                    $field['list_values'][$listKey] = __($listVal);
                }
            }   

            $field['value'] = $this->formatValue($cave->{$key}, $key, $field);
        }
        if($outpoutFormat === self::AS_DATATABLES){
            
            $out = [];
            foreach($output as $key => $value){
                $out[$key] = $value['value'];
            }
            return $out;
        }

        return match ($outpoutFormat) {
            self::AS_STD_OBJ  => (object) $output,
            default           => $output, // by default AS_ARRAY
        };
    }

    protected function formatValue(mixed $value, string $key, array $field): mixed
    {
        //empty val
        if ($value === null || $value === '') {
            if($field['data_type'] === 'delimitedArray'){
                return [];
            }
            return '---';
        }
        
        //special list case
        if(isset($field['list_values'])){
            
            return __($field['list_values'][$value]);
        }

        //all other type of data
        return match ($field['data_type']) {
            'bool' => $value
                ? __('varcave.general.yes')
                : __('varcave.general.no'),
            'timestamp' => date('d/m/Y', $value),
            'date' => $value?->format('d/m/Y'),
            'string' => (string) trim($value . ' ' . $field['unit']),
            'number' => (int) $value . ' ' . $field['unit'],
            'delimitedArray' => explode($this->separatorOptions[ Str::lower($key)] ??  $this->separatorOptions['default'], $value  ),
            default => $value,
        };
    }
}
