<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
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

        if($asArray){
            return $list->toArray();
        }
        else{
            return $list;
        }
    }

    public static function getListValues(string $name){
        Log::debug(__METHOD__ . ' called.');
        $listElement = self::where('list_name', trim($name))
            ->orderBy('sort_order','asc')
            ->get();
        
        $list = [];
        foreach($listElement as $el){
                if($el['i18n_key'] != null && (isset($el['i18n_key']) && Lang::has($el['i18n_key'])) ){
                    $list[] = Str::upper(__($el['i18n_key']));
                }else{
                    $list[] = $el['value'];
                }
        }  

        return $list;
    
    }
}
