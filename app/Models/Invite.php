<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invite extends Model {
    public $timestamps = false;

    protected $casts = [
        'used_at' => 'datetime',
    ];
}
