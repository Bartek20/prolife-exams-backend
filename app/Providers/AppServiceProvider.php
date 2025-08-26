<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Events\TokenAuthenticated;

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
        // Token expire since last used
        Event::listen(TokenAuthenticated::class, function (TokenAuthenticated $event) {
            $token = $event->token;
            if ($token->tokenable_type != 'App\Models\User') return;
            $start = $token->last_used_at ?? $token->created_at;
            $end = $token->expires_at;
            $duration = $start->diffInSeconds($end);
            $expire = now()->addSeconds($duration);
            $token->expires_at = $expire;
            // Skipping save as Sanctum updates "last_used_at" field and saves with this change
        });

        RateLimiter::for('account-create', function (Request $request) {
            return [
                Limit::perMinute(3)->by($request->ip()),
                Limit::perHour(10)->by($request->ip()),
            ];
        });
        RateLimiter::for('login', function (Request $request) {
           return [
               Limit::perMinute(3)->by($request->ip()),
               Limit::perHour(10)->by($request->ip()),
           ];
        });
        RateLimiter::for('exam-config', function(Request $request) {
            return Limit::perMinute(6)->by($request->ip());
        });
        RateLimiter::for('exam-start', function(Request $request) {
            return [
                Limit::perMinute(3)->by($request->ip()),
                Limit::perHour(6)->by($request->ip())
            ];
        });
    }
}
