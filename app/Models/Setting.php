<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    // Mass assignable attributes
    protected $fillable = [
        'name',
        'value',
        'type',
        'category',
        'is_advanced_option',
        'legacy_mtime',
    ];



    /**
     * Get settings by it key
     */
    public static function get(string $key, $default = null)
    {
        $setting = self::where('name', $key)->first();
        return $setting ? $setting->value : $default;
    }

    /**
     * Accès à la valeur correctement castée selon le type.
     */
    public function getValueAttribute($value)
    {
        return match($this->type) {
            'int'    => (int) $value,
            'float'  => (float) $value,
            'bool'   => (bool) $value,
            //'json'   => json_decode($value,true),
            'datetime' => $value ? Carbon::parse($value) : null,
            default  => $value,
        };
    }

    /**
     * Mutateur pour setter automatiquement le value selon le type
     */
    public function setValueAttribute($val)
    {
        $this->attributes['value'] = match($this->type) {
            //'json'   => json_encode($val),
            'bool'   => $val ? '1' : '0',
            'int', 'float', 'string', 'datetime' => (string)$val,
            default  => (string)$val,
        };
    }
}
