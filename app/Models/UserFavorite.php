<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserFavorite extends Model
{
    protected $fillable = ['cave_uuid'];

    /**
     * Eloquent relation to users
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
