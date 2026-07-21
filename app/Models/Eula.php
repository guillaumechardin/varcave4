<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Eula extends Model
{
    protected $fillable = [
        'content',
        'lang',
    ];

    public function getAvailableLang()
    {
        $lang = DB::table('eulas')
        ->select('lang')
        ->distinct()
        ->pluck('lang');
        
        return $lang;
    }


}
