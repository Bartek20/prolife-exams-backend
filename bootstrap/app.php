<?php

use App\Http\Middleware\ImplicitBinding;
use App\Http\Middleware\Sanctum;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Sentry\Laravel\Integration as Sentry;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->validateCsrfTokens(except: [
            'api/sentry',
        ]);
        $middleware->alias([
            'sanctum' => Sanctum::class
        ]);
        $middleware->replaceInGroup(
            'api',
            SubstituteBindings::class,
            ImplicitBinding::class
        );
    })
    ->withExceptions(function (Exceptions $exceptions) {
        Sentry::handles($exceptions);
        $exceptions->render(function (ThrottleRequestsException $throwable) {
            $headers = $throwable->getHeaders();

            $retryAfter = now()->addSeconds($headers['Retry-After'])->toIso8601String();

            return response()->json([
                'success' => false,
                'error' => 'Rate limit exceeded',
                'retry_after' => $retryAfter,
            ], 429, $headers);
        });
    })->create();
