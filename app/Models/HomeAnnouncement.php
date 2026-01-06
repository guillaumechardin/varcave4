<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HomeAnnouncement extends Model
{
    /**
     * Get the associated creator
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator');
    }

    /**
     * Get the associated editor
     */
    public function lastEditor(): BelongsTo
    {
        return $this->BelongsTo(User::class, 'last_editor');
    }

    /**
     * Get last N announces, 
     */
    public static function latestAnnouncements(int $count, string $sortOrder = 'desc', string $sortField = 'created_at' )
    {
        return self::orderBy($sortField, $sortOrder)
                    ->limit($count)
                    ->get();
    }
}
