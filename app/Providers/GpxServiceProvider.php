<?php

namespace App\Providers;

use App\Services\GpxService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class GpxServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(GpxService::class, function (Application $app) {
            return new GpxService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
