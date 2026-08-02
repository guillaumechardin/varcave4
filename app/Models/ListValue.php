<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use phpDocumentor\Reflection\Types\Boolean;

class ListValue extends Model
{
    protected $fillable = [
        'list_name',
        'value',
        'i18n_key',
        'sort_order',
        'is_active',
        'created_at',
        'updated_at',
    ];
    public static function getByListName(string $name, bool $asArray = true) :array|Collection
    {
        Log::debug(__METHOD__ . ' called.');
        Log::debug('  fetch list: '. $name);
        
        $list = self::where('list_name', trim($name))
            ->orderBy('sort_order','desc')
            ->get();
        
        if ($list->isEmpty()) {
           throw (new ModelNotFoundException())->setModel(self::class);
        }

        /**
         * Not transformed for now keep i18n in view
        $list->transform(function (ListValue $item) {
            $item->i18n_key = __($item->i18n_key);
            return $item;
        });
        */

        if($asArray){
            return $list->toArray();
        }
        else{
            return $list;
        }
    }
}
