<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class FeaturedCave extends Model
{
    protected $fillable = [
         'cave_id',
         'is_active',
    ];

    public function cave()
    {
        return $this->hasOne(Cave::class);
    }

    /**
     * Save given cave to featured cave if CaveFile['photos'] exists
     * @param $cave Cave model with caveFiles relation
     * @return null or created FeaturedCave 
     */
    public static function setAsFeatured(Cave $cave): ?FeaturedCave
    {
        Log::debug(__METHOD__ . ' called.');
        Log::info('Add cave:' . $cave->id . ' to featured caves');

        $hasPhotos = $cave->caveFiles()
            ->where('file_type', 'photos')
            ->exists();

        if (!$hasPhotos) {
            Log::error('cave has no photos');
            return null;
        }

        FeaturedCave::query()->update(['is_active' => 0]);

        return FeaturedCave::firstOrCreate(
            ['cave_id' => $cave->id],
            [
                'is_active' => 1,
            ]
        ); 
    }
}
