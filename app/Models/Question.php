<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Question extends Model {
    use HasUuids;
    use SoftDeletes;

    public $incrementing = false;
    protected $primaryKey = 'uuid';
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'question',
        'options',
        'answer',
        'images'
    ];


    protected function casts(): array {
        return [
            'options' => 'array',
            'images' => 'json',
        ];
    }
}
