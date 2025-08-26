<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class RequestBase
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    abstract public function handle(Request $request, Closure $next): Response;
    static protected function fail($error, $code = 400): JsonResponse {
        return new JsonResponse([
            'success' => false,
            'error' => $error,
        ], $code);
    }
}
