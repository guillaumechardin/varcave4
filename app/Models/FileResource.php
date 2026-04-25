<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FileResource extends Model
{
    protected $casts = [
        'access_rights' => 'array',
    ];

    protected $fillable = [
        'user_id',
        'file_resource_group_id',
        'name',
        'file_path',
        'original_file_name',
        'description',
        'access_rights',
        'is_hidden',
        'sort_order',
    ];

    public function fileResourceGroup()
    {
        return $this->belongsTo(FileResourceGroup::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
