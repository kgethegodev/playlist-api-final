<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\GeminiService;

class GeminiServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(GeminiService::class, function ($app) {
            return new GeminiService();
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
