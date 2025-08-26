<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Log;

function generateEd25519KeyPair(): array
{
    $keyPair = sodium_crypto_sign_keypair();

    $privateKey = sodium_crypto_sign_secretkey($keyPair);
    $publicKey = sodium_crypto_sign_publickey($keyPair);

    return [
        'private' => $privateKey,
        'public' => $publicKey,
    ];
}

class Exam extends Model {
    protected static function booted(): void {
        static::creating(function ($exam) {
            $keys = generateEd25519KeyPair();

            $exam->private_key = $keys['private'];
            $exam->public_key = $keys['public'];
        });
    }

    public $timestamps = false;

    public $fillable = [
        'name',
        'access_code',
        'start_time',
        'end_time',
        'question_number',
        'passing_score',
        'can_go_back',
        'is_global_duration',
        'show_results'
    ];

    protected $hidden = [
        'public_key',
        'private_key',
    ];

    protected function casts(): array {
        return [
            'start_time' => 'datetime',
            'end_time' => 'datetime',
            'show_results' => 'boolean',
            'can_go_back' => 'boolean',
            'is_global_duration' => 'boolean',
        ];
    }

    public function getStatusAttribute(): string {
        $now = now();
        if ($this->start_time > $now) {
            return 'upcoming';
        } elseif ($this->end_time && $this->end_time < $now) {
            return 'finished';
        } else {
            return 'ongoing';
        }
    }

    public function responses(): HasMany {
        return $this->hasMany(Response::class, 'exam_id');
    }
    public function stats(): HasMany {
        return $this->hasMany(Statistic::class, 'exam_id');
    }
}
