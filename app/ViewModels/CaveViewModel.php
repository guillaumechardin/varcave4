<?php 
namespace App\ViewModels;

use App\Models\Cave;
use App\Models\Page;
use App\Models\Field;
use App\Models\PageField;

class CaveViewModel
{
    public function __construct(protected Cave $cave, protected Page $page)
    {
        //default constructor
    }

    /*
     * logique d'origine
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
            'string'    => (string) $value,
            'number' => (int) $value,
            default   => $value,
        };
    }
}
