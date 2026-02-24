<?php

namespace App\Models;


use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\belongsTo;

class CaveFile extends Model
{
    //relation to cave table
    public function cave(): belongsTo
    {
        return $this->belongsTo(Cave::class);
    }


    /**
     * Retrieve files associated with a cave.
     *
     * Fetches up to a given number of files linked to a cave identified by its UUID.
     * Optionally filters files by type. If the file type is set to '*' (default), no filtering
     * is applied and all file types are returned.
     *
     * @param string $caveUuid UUID v4 identifying the cave
     * @param string $fileType File type filter ('*' to retrieve all types)
     * @param int    $count    Maximum number of files to retrieve
     *
     * @return \Illuminate\Database\Eloquent\Collection
     *         Returns a collection of files on success, or null if the cave does not exist
     */
    public static function get(Cave $cave, string $fileType = '*', int $count = 100): Collection
    {
        Log::debug(__METHOD__ . ' called');
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

    public static function hasFilesType():bool
    {

        return true;

    }
}
