<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

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
        $this->configureRateLimiting();
    }

    /**
     * Configure the rate limiters for the application.
     */
    protected function configureRateLimiting(): void
    {
        // Rate limit para API geral
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // Rate limit para login - prevenir brute force
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        // Rate limit para transações - prevenir spam/fraude
        RateLimiter::for('transactions', function (Request $request) {
            return [
                // Máximo 10 transações por minuto
                Limit::perMinute(10)->by($request->user()?->id),
                // Máximo 100 transações por hora
                Limit::perHour(100)->by($request->user()?->id),
            ];
        });

        // Rate limit para depósitos
        RateLimiter::for('deposits', function (Request $request) {
            return [
                // Máximo 5 depósitos por minuto
                Limit::perMinute(5)->by($request->user()?->id),
                // Máximo 20 depósitos por hora
                Limit::perHour(20)->by($request->user()?->id),
            ];
        });

        // Rate limit para transferências
        RateLimiter::for('transfers', function (Request $request) {
            return [
                // Máximo 5 transferências por minuto
                Limit::perMinute(5)->by($request->user()?->id),
                // Máximo 50 transferências por hora
                Limit::perHour(50)->by($request->user()?->id),
            ];
        });

        // Rate limit para reversões
        RateLimiter::for('reversals', function (Request $request) {
            return [
                // Máximo 3 reversões por minuto
                Limit::perMinute(3)->by($request->user()?->id),
                // Máximo 10 reversões por hora
                Limit::perHour(10)->by($request->user()?->id),
            ];
        });
    }
}
