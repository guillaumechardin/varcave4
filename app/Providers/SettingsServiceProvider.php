<?php
namespace App\Providers;

use App\Services\SettingsService;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class SettingsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        
        $this->app->singleton(SettingsService::class, function ($app) {
            return new SettingsService();
        });
    }

    public function boot(): void
    {
        // Share settings an all Blade view
        /**
         * REMOVE LATER AFTER LEGACY MIGRATION
         */
        if (App::runningInConsole()) {
            return;
        }
        //**END REMOVE LATER */
        View::share('settings', $this->app->make(SettingsService::class));
    }
}
