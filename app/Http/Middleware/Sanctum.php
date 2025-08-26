<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class Sanctum {
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next, ...$abilities): Response {
        if (!$request->bearerToken()) return $this->unauthenticated();
        $user = Auth::guard('sanctum')->user();
        if (!$user) return $this->unauthenticated();

        $token = $user->currentAccessToken();
        foreach ($abilities as $abilityList) {
            $can = false;
            foreach (explode('|', $abilityList) as $ability) {
                if ($token->can($ability)) {
                    $can = true;
                    break;
                }
            }
            if (!$can) return $this->unauthorized();
        }

        if ($request->attributes->has('is404') && $request->attributes->get('is404')) {
            return response()->json([
                'success' => false,
                'error' => 'Resource not found.',
                'missingModel' => $request->attributes->get('missingModel')
            ], 404);
        }

        if ($user->getTable() == 'users') {
            Auth::setUser($user);
        }
        else {
            $request->attributes->add([
                'response' => $user,
            ]);
        }

        return $next($request);
    }

    private function unauthenticated(): Response {
        return response()->json([
            'success' => false,
            'error' => 'Unauthenticated.'
        ], 401);
    }

    private function unauthorized(): Response {
        return response()->json([
            'success' => false,
            'error' => 'Unauthorized.'
        ], 403);
    }
}
