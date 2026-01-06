<?php

namespace App\Models;


use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\belongsTo;

class CaveFile extends Model
{
    //relation to cave table
    public function cave(): belongsTo
    {
        return $this->belongsTo(Cave::class);
    }


    /** 
     * get files for a cave
     * 
     */
    public static function get(string $caveUuid, string $fileType = '*', int $count = 100)
    {
        $cave = Cave::getFromUuid($caveUuid);

        if(!$cave)
        {
            return null;
        }
        
        $files =  self::where('cave_id', $cave->id)
                    ->when($fileType !== '*', function ($q) use ($fileType) {
                            $q->where('file_type', $fileType);
                    })
                    ->limit($count)
                    ;
        Log::debug(__METHOD__ . ' caveFiles.', [
                        'caveId' => $cave->id,
                        'sql' => $files->toSql(),
                        'bindings' => $files->getBindings(),
                        
        ]);
        return $files->get();
    }
}
