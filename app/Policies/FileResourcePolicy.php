<?php

namespace App\Policies;

use App\Models\FileResource;
use App\Models\Role;
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
        Log::debug('check user: ' . $user?->id . ' resource access to '. $res->name);
        $accessRights = $res->access_rights;

        $public = Role::where('name', 'public')->firstOrFail();
        
        //accept public file download if file is set public
        if(in_array($public->name, $accessRights)){
            Log::debug('Allowed: anonymous/public file');
            return true;
        }

        if (!$user) {
            Log::debug('Denied: anonymous user');
            return false; 
        }
        //check if user is admin or member of any roles required by file access rights
        if($user->hasRole($accessRights) || $user->hasRole('admin')) {
            Log::debug('Allowed: user has access to file');
            return true;
        }

        Log::debug('Denied: user has no access to file');
        return false;
        
    }

    public function isResourceAdmin(?User $user): bool
    {
        Log::debug(__METHOD__  . ' called.');

        if (!$user) {
            Log::debug('Denied: user is anonimous');
            return false; 
        }

        //check if user is admin or member of any roles required by file access rights
        if($user->isAdmin() || $user->hasRole('resource-admin')) {
            Log::debug('Allowed: has Admin role or is `resource-admin` ');
            return true;
        }
        return false;
    }
}
