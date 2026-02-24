<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class Cave extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $dates = ['deleted_at'];

    //relation to cave_changelog table
    public function changelog(): HasMany
    {
        return $this->hasMany(CaveChangelog::class)
            ->orderByDesc('created_at');
    }

    //relation to cave_coordinates
    public function caveCoordinates(): HasMany
    {
        return $this->hasMany(caveCoordinates::class);
    }

    //relation to cave_files table
    public function caveFiles(): HasMany
    {
        return $this->hasMany(CaveFile::class);
    }

    public static function getByUuid(string $uuid): ?Cave
    {
        Log::debug(__METHOD__ . ' called.', [
            'uuid' => $uuid,
        ]);

        $valid = Validator::make(
            ['uuid' => $uuid],
            ['uuid' => 'required|uuid']
        )->passes();

        if (!$valid) {
            Log::warning(__METHOD__ . ' cave not found.', ['uuid' => $uuid]);
            return null;
        }
        return self::where('uuid', $uuid)->first() ;
    }

    /**
     * Checks if the cave has a given file type
     *
     * @param string $type The file type to check, e.g., 'cave_maps'
     * @return bool
     */
    public function hasFileType(string $type): bool
    {
        return $this->caveFiles()->where('file_type', $type)->exists();
    }

    
}
