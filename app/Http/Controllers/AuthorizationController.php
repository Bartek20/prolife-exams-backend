<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthorizationRequest;
use App\Models\User;
use App\Services\AuthorizationService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class AuthorizationController extends Controller
{
    public function __construct(private AuthorizationService $service) {
    }

    public function tokenize(AuthorizationRequest $request) {
        $user = User::where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'These credentials do not match our records.',
            ], 401);
        }

        $token = $this->service->getToken($user);
        return response()->json([
            'success' => true,
            'message' => 'Authorized successfully',
            'token' => $token,
        ]);
    }

    public function logout(Request $request) {
        $request->user()->token()->delete();
        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully'
        ]);
    }

    public function check(Request $request) {
        return response()->json([
            'success' => true,
            'message' => 'Token is valid',
            'area' => $request->user()->currentAccessToken()->abilities
        ]);
    }
}
