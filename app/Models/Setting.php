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
        'is_user_overridable',
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
    public static function getValue(string $key)
    {
        $setting = self::where('name', $key)->firstOrFail();

        return match($setting->type) {
            'int'    => (int) $setting->value,
            'float'  => (float) $setting->value,
            'bool'   => (bool) $setting->value,
            'json'   => json_decode($setting->value, true),
            'datetime' => $setting->value ? Carbon::parse($setting->value) : null,
            default  => $setting->value, //string 
        };
    }

    /**
     * setter automatique selon le type
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
