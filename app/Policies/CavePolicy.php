<?php

namespace App\Policies;

use App\Models\Cave;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use PhpParser\Node\Expr\BooleanNot;

class CavePolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function showAllCaveDetails(User $user, Cave $cave): bool
    {
        Log::debug(__METHOD__  . ' called.');
        if($user->hasRole('user')) {
            return true;
        }
        return false;
    }

    /*to be deleted 04/02/2026*/
    /*public function useAdvancedSearch(User $user, Cave $cave): bool
    {
        Log::debug(__METHOD__  . ' called.');
        if($user->hasRole('user')) {
            return true;
        }
        return false;
    }*/
}
