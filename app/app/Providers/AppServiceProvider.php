<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\Cache\RedisCacheService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(RedisCacheService::class, function ($app) {
            return new RedisCacheService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
