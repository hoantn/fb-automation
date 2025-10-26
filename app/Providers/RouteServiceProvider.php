<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Where to redirect users after login.
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, and route groups.
     */
    public function boot(): void
    {
        // (Tuỳ chọn) Giới hạn tốc độ cho nhóm 'api'
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // Khai báo nơi nạp routes
        $this->routes(function () {
            // Route API: /api/*
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            // Route Web: /
            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }
}
