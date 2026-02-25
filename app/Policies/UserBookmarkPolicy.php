<?php
namespace App\Policies;

use App\Models\User;
use App\Models\UserBookmark;
use Illuminate\Support\Facades\Log;
use Illuminate\Auth\Access\Response;


class UserBookmarkPolicy
{
    public function deleteBookmark(User $user, UserBookmark $bookmark)
    {
        Log::debug(__METHOD__  . ' called.');
        if($user->id == $bookmark->user_id) { //is bookmark owner
            return Response::allow();
        }
        Log::error( $user->id . ' is not owner of bookmark:' .$bookmark->id);
        return Response::deny( __('varcave.profile.not-bookmark-owner') );
    }
}