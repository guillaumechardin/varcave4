<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\belongsTo;

class CaveChangelog extends Model
{
    // Mass assignable attributes
    protected $fillable = [
        'cave_id',
        'modification_note',
        'author',
        'is_visible',
    ];

    //relation to cave table
    public function cave(): belongsTo
    {
        return $this->belongsTo(Cave::class);
    }

    public static function lastestCaveChangeLog(int $count, string $sortOrder = 'desc', string $sortField = 'created_at')
    {
        return self::orderBy($sortField, $sortOrder)
                    ->limit($count)
                    ->get();
    }
}
