<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class SetLocale
{
    public function handle($request, Closure $next)
    {
        /* Not in use for now
          //Check lang param in url
        if ($request->has('lang')) {
            $lang = $request->get('lang');
            Session::put('user_lang', $lang);
        }
        */

        //  Check if user variable is present
        if (Session::has('user_lang')) { 
            $locale = Session::get('user_lang'); // ****  not implemented now **** use/check UserPreferenceService  
            Log::info('User force language from account settings');
        }
        //  Detect from browser
        elseif ($request->server('HTTP_ACCEPT_LANGUAGE')) {
            $locale = mb_strtolower(substr($request->server('HTTP_ACCEPT_LANGUAGE'), 0, 2)); //keep only first defined language, to lower.
            Log::debug('Get user language from browser (locale:'.$locale.')');
        }
        //  Fallback to Laravel configuration
        else {
            $locale = config('app.locale');
        }


        // Apply lang
        App::setLocale($locale);

        //process request on  next middleware 
        
        $response = $next($request);
        return $response;

        // --- Inject i18n script only if response is HTML and file present ---
        /**
         * JS file is no more used
         */

        /*
        $jsFile = public_path('varcave/langcache/lang.' . $locale . '.js');
        if (file_exists($jsFile) && 
            $response instanceof \Illuminate\Http\Response && 
            str_contains($response->headers->get('Content-Type'), 'text/html')
        ){
            $content = $response->getContent();

            // Insert before </body>
            $injection = '<script src="/varcave/langcache/lang.' . $locale . '.js" defer></script>';

            $content = str_replace('</head>', $injection . "\n</head>", $content);

            $response->setContent($content);
        }
        else // Load only default english i18n lang module
        {
            $content = $response->getContent();
            // Insert before </head>
            $injection = '<script src="/rigmanager/langcache/lang.en.js" defer></script>';
            $content = str_replace('</head>', $injection . "\n</head>", $content);
            $response->setContent($content);
        }
        return $response;
        */
    }
}
