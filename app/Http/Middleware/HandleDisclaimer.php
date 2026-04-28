<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class HandleDisclaimer
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            Log::debug('eula acceptance skipped, user un-authenticated');
            return $next($request);
        }

        //present eula to user if general configuration is set AND user has not yet accepted eula
        if (Setting::get('user_must_accept_EULA') && !$user->eula_accepted ) {
            if (!$request->routeIs('varcave.profile.eula*')) {
                Log::debug('eula not accepted, redirect to EULA');
                return redirect()->route('varcave.profile.eula.show');
            }
        }

        return $next($request);
    }
}
