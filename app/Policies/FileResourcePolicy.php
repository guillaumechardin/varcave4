<?php

namespace App\Policies;

use App\Models\FileResource;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class FileResourcePolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function getResource(?User $user, FileResource $res): bool
    {
        Log::debug(__METHOD__  . ' called.');
        $accessRights = json_decode($res->access_rights);

        //accept public file download if file is set public
        if(in_array('public', $accessRights)){ 
            return true;
        }

        if (!$user) {
            Log::debug('anonymous user');
            return false; 
        }
        //check if user is admin or member of any roles required by file access rights
        if($user->hasRole($accessRights) || $user->hasRole('admin')) {
            return true;
        }
        return false;
    }

    public function isResourceAdmin(?User $user): bool
    {
        Log::debug(__METHOD__  . ' called.');

        if (!$user) {
            return false; 
        }

        //check if user is admin or member of any roles required by file access rights
        if($user->isAdmin() || $user->hasRole('resource-admin')) {
            return true;
        }
        return false;
    }
}
