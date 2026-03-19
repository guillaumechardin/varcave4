<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\UserPreferenceService;

class UserPreferenceServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(UserPreferenceService::class, function ($app) {
            return new UserPreferenceService();
        });
    }
}