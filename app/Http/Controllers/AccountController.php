<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateAccountRequest;
use App\Services\AuthorizationService;
use App\Services\InviteService;

class AccountController extends Controller
{
    public function __construct(private InviteService $inviteService, private AuthorizationService $authorizationService) {
    }

    public function store(CreateAccountRequest $request) {
        $invite = $this->inviteService->getInvite($request->invite_code);

        if (!$invite || $invite->email !== $request->email) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid invite code or email does not match the invite.'
            ], 401);
        }

        if ($invite->used_at) {
            return response()->json([
                'success' => false,
                'message' => 'This invite has already been used.'
            ], 400);
        }

        $user = $this->inviteService->redeemInvite($invite, $request->validated());

        $token = $this->authorizationService->getToken($user);

        return response()->json([
            'success' => true,
            'message' => 'Account created',
            'token' => $token
        ]);
    }
}
