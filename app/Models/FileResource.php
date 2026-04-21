<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FileResource extends Model
{

    public function fileResourceGroup()
    {
        return $this->belongsTo(FileResourceGroup::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
