<?php

namespace App\Actions\Varcave;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;


class CheckUserCanAuthenticate
{
    public function __invoke($request, $next)
    {
        $user = Auth::user();
        
        $status = $user->canAuthenticate();
        
        if ($status !== true){
            Auth::logout();
            //$status is a string
            return redirect(route('login'))->with('accountState', $status);
        }
        
        return $next($request);
    }
}
