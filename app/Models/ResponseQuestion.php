<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResponseQuestion extends Model
{
    public $timestamps = false;

    protected static function booted(): void {
        static::updating(function($model) {
            $model->last_modified_at = now();
        });
    }

    protected $fillable = [
        'response_uuid',
        'question_uuid',
        'index',
        'answer'
    ];

    protected $casts = [
        'generated_at' => 'datetime',
        'last_modified_at' => 'datetime',
    ];

    public function response(): BelongsTo {
        return $this->belongsTo(Response::class, 'response_uuid', 'uuid');
    }
    public function question(): BelongsTo {
        return $this->belongsTo(Question::class, 'question_uuid', 'uuid')->withTrashed();
    }

    public function getIsCorrectAttribute(): bool {
        $correct = $this->question()->select('answer')->value('answer');
        return !is_null($this->answer) && $correct == $this->answer;
    }
}
