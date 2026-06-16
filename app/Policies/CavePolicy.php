<?php

namespace App\Policies;

use App\Models\Cave;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class CavePolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function showAllCaveDetails(User $user): bool
    {
        Log::debug(__METHOD__  . ' called.');
        if($user->hasRole('user')) {
            Log::debug('Has role succeed');
            return true;
        }
        Log::debug('Has role failed');
        return false;
    }

    public function downloadCoordinates(User $user, Cave $cave): bool
    {
        Log::debug(__METHOD__  . ' called.');
        if($user->hasRole('user')) {
            return true;
        }
        return false;
    }

    public function downloadPdf(User $user, Cave $cave): bool
    {   
        Log::debug(__METHOD__  . ' called.');
        if($user->hasRole('user')) {
            Log::debug('Has role succeed');
            return true;
        }
        Log::debug('Has role failed');
        return false;
    }

    public function showRescueInfo(User $user, Cave $cave): bool
    {
        Log::debug(__METHOD__  . ' called.');
        if($user->hasRole('rescue-data-reader') || $user->hasRole('admin') ) {
            Log::debug('Has role succeed');
            return true;
        }
        Log::debug('Has role failed');
        return false;
    }

    public function updateCave(User $user, Cave $cave): bool
    {
        Log::debug(__METHOD__  . ' called.');
        if($user->hasRole('cave-editor') || $user->hasRole('admin') ) {
            Log::debug('Has role succeed');
            return true;
        }
        Log::debug('Has role failed');
        return false;
    }
}
