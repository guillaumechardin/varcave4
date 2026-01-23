<?php

namespace App\Models;

use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cave extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    //relation to cave_changelog table
    public function changelog(): HasMany
    {
        return $this->hasMany(CaveChangelog::class);
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
        return self::with('caveFiles')->where('uuid', $uuid)->first();
    }
}
