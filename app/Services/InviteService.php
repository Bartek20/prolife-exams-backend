<?php

namespace App\Services;

use App\Models\Invite;
use App\Models\User;

class InviteService {
    public function getInvite($code): ?Invite {
        return Invite::whereRaw('invite_code = ? COLLATE BINARY', [$code])->first();
    }

    public function redeemInvite(Invite $invite, $data): User {
        $user = User::create($data);

        $invite->update([
            'used_at' => now(),
        ]);

        return $user;
    }
}
