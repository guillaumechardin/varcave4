<?php

namespace App\Models;

use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;

class PageField extends Model
{
    protected $fillable = ['page_key', 'field_id', 'section_key', 'is_visible', 'sort_order'];

    public $fields = [];
    
    protected $casts = [
      'is_visible' => 'boolean',
    ];

    public function __construct (){

    }

    public function field()
    {
        return $this->belongsTo(Field::class);
    }

    protected static function makeRuntimePageField(string $key)
    {
        $pageField = new PageField([
            'section_key' => 'runtime',
            'is_visible'  => 1,
            'sort_order'  => 0,
        ]);

        $pageField->setRelation(
            'field',
            new Field([
                'key'       => $key,
                'data_type' => 'string',
                'label'     => $key,
            ])
        );

        return $pageField;
    }
}