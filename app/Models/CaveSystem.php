<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CaveSystem extends Model
{
    //relation to : caves
    public function caves()
    {
        return $this->hasMany(Cave::class);
    }
}
