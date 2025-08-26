<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Statistic extends Model
{
    public $timestamps = false;

    protected $fillable = ['exam_id', 'date', 'created', 'completed'];

    protected $casts = [
        'created' => 'integer',
        'completed' => 'integer',
    ];

    public function exam(): HasOne {
        return $this->hasOne(Exam::class, 'id', 'exam_id');
    }
}
