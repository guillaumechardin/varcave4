<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageField extends Model
{
    protected $fillable = ['page_key', 'field_id', 'section_key', 'sort_order'];

    public function field()
    {
        return $this->belongsTo(Field::class);
    }

}
