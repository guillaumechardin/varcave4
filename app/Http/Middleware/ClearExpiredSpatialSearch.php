<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Removes expired spatial search data from session variable if exists.
 *
 * This middleware checks the spatial search session data on each web
 * web request  and removes it when its expiration time has been reached.
 */
class ClearExpiredSpatialSearch
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $spatialSearch = $request->session()->get('lastSpacialSearchQuery');

        if ($spatialSearch && now()->greaterThan($spatialSearch['expires_at'])){
            $request->session()->forget('lastSpacialSearchQuery');
        }

        return $next($request);
    }
}
