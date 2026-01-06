<?php

namespace App\Models;

use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cave extends Model
{
    //relation to cave_changelog table
    public function changelog(): HasMany
    {
        return $this->hasMany(CaveChangelog::class);
    }

    //relation to cave_files table
    public function caveFiles(): HasMany
    {
        return $this->hasMany(caveFile::class);
    }

    public static function getFromUuid(string $uuid)
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
        return self::where('uuid', $uuid)->first();
    }
}
