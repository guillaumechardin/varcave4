<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\belongsTo;
use Illuminate\Support\Facades\Log;

class CoordinateSystemHandler extends Model
{
    //relation to list_values table
    public function listValue(): belongsTo
    {
        return $this->belongsTo(ListValue::class);
    }

    public static function getAllCrs()
    {
        Log::debug(__METHOD__ . ' called.');

        $allCrs = self::where('enabled', 1)
        ->with('ListValue')
        ->select([
            'id',
            'list_value_id',
            'epsg_code',
            'epsg_name',
            'js_handler',
            'php_handler',
            'proj4_string',
        ])
        ->get()
        ->toArray();

        $crss = array();
        foreach($allCrs as $crs){
            $c = $crs;
            unset($c['list_value']);
            $c['list_value_value'] = $crs['list_value']['value'];
            $c['list_value_i18n_key'] = $crs['list_value']['i18n_key'];
            $crss[] = $c;
        }
        return $crss;
    } 
}
