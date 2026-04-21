<?php

namespace App\Models;

use App\Models\FileResource;
use Illuminate\Database\Eloquent\Model;

class FileResourceGroup extends Model
{
    public function fileResource()
    {
        return $this->hasMany(FileResource::class);
    }
}
