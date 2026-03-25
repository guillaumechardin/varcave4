<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Support\Facades\Log;

class UserPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function fetchUser(User $user, User $fetchedUser): bool
    {
        Log::debug(__METHOD__  . ' called.');
        if($user->hasRole('admin')) {
            //admin role user can fetch any user
            return true;
        }
        elseif($fetchedUser->id === $user->id)
        {
            //user can fetch his data
            return true;
        }
        else{
            Log::warning('DENIED,  user: '. $user->id . ' tryed to fetch user: ' . $fetchedUser->id);
            return false;
        }
    }
}
