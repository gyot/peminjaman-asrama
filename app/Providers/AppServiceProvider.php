<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        // $this->configureRateLimiting();

        // $this->routes(function () {
            Route::middleware('web')
                ->group(base_path('routes/web.php'));

            // Tambahkan baris ini untuk mendaftarkan routes/api.php
            Route::prefix('api')
                ->middleware('api')
                ->group(base_path('routes/api.php'));
        // });
    }

    // protected function configureRateLimiting()
    // {
    //     RateLimiter::for('api', function ($request) {
    //         return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
    //     });
    // }
}
