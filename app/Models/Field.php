<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Field extends Model
{
    protected $fillable = [
        'key', 
        'data_type',
        'storage_type',
        'storage_target',
        'unit',
    ];

    public function pageFields()
    {
        return $this->hasMany(PageField::class);
    }

    /**
     * get the current i18n label for a key
     */
    public function label(): string
    {
        return __('varcave.table_cave.' . $this->key);
    }
}