<?php

namespace App\Services;

use App\Models\Cave;
use App\Models\Field;


use Illuminate\Support\Facades\Log;


class CaveService
{   
    protected $fields = null;

    public function __construct()
    {

    }


    private function getFieldsDefinition()
    {
        Log::debug(__METHOD__ . ' called');
        $this->fields = Field::all();
    }


    public function formatValue(mixed $value, string $key, string $dataType): mixed
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
        
        //special list case
        if($fieldDef['storage_type'] == 'list' && $listValues != null){
            return __($listValues[$value]);
        }

        //all other type of data
        return match ($fieldDef['data_type']) {
            'bool' => $value
                ? __('varcave.general.yes')
                : __('varcave.general.no'),
            'timestamp' => date('d/m/Y', $value),
            'date' => $value?->format('d/m/Y'),
            'string' => (string) trim($value . ' ' . $fieldDef['unit']),
            'number' => (int) $value . ' ' . $fieldDef['unit'],
            'delimitedArray' => explode($this->separatorOptions[ Str::lower($key)] ??  $this->separatorOptions['default'], $value  ),
            default => $value,
        };
    }
}