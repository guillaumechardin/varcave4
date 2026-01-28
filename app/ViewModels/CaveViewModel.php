<?php 
namespace App\ViewModels;

use App\Models\Cave;
use App\Models\Page;
use App\Models\Field;
use App\Models\PageField;
use Illuminate\Support\Str;

class CaveViewModel
{
    private $separatorOptions = [
       'bibliography' => '/',
       'default'   => ',',
    ];

    public function __construct(protected Cave $cave, protected Page $page)
    {
        //default constructor
    }

    /*
     * return a list of formated cave data from their respective display page
     */
    public function getFields(): array
    {
        $output = [];

        foreach ($this->page->pageFields as $pageField) {
            $field = $pageField->field  ;
            $key   = $field->key;

            $raw = $this->cave->$key ?? null;
        
            $output[$key] = [
                'label' => __('varcave.table_cave.'.$key),
                'value' => $this->formatValue($raw, $field),
                'type'  => $field->data_type,
                'sort_order' => $pageField->sort_order,
            ];
        }     
        return $output;
    }

    protected function formatValue(mixed $value, Field $field): mixed
    {
        if(empty($value)){
            $value = '---';
            $field->data_type = 'string';
        }

        return match ($field->data_type) {
            'bool' => $value ? __('varcave.general.yes') : __('varcave.general.no'),
            'timestamp'    => date('d/m/Y', $value),
            'date' => $value?->format('d/m/Y'),
            'string'    => (string) $value . ' ' . $field->unit,
            'number' => (int) $value. ' ' . $field->unit,
            'delimitedArray' => explode($this->separatorOptions[ Str::lower($field->key)] ??  $this->separatorOptions['default'], $value  ),
            default   => $value,
        };
    }
}
