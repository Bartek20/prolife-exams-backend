<?php

namespace App\Services;

use App\Models\User;

class AuthorizationService {

    public function getToken(User $user) {
        if ($user->role == 'teacher') return $this->getAdminToken($user);
        return $this->getStudentToken($user);
    }
    private function getAdminToken($user) {
        return $this->createToken($user, 'admin');
    }
    private function getStudentToken($user) {
        return $this->createToken($user, 'student');
    }


    private function createToken($user, $area) {
        $token = $user->createToken('auth_token', [$area . '_area'], now()->addHours(3));
        return $this->extractPlainToken($token);
    }
    private function extractPlainToken($token) {
        return explode('|', $token->plainTextToken)[1];
    }
}
