<?php
namespace App\Providers;

use App\Services\UserPreferenceService;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class UserPreferenceServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(UserPreferenceService::class, function ($app) {
            return new UserPreferenceService();
        });
    }

    public function boot(): void
    {
        // Share UserPreferenceService in all Blade view
        View::share('UserPreference', $this->app->make(UserPreferenceService::class));
    }
}