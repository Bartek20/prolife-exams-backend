<?php

namespace App\Models;

use DateInterval;
use DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

function getEndTime($start_time, $duration, $multiplier) {
    [$hours, $minutes, $seconds] = explode(':', $duration);

    $hours *= $multiplier;
    $minutes *= $multiplier;
    $seconds *= $multiplier;

    return (new DateTime($start_time))
        ->add(new DateInterval(sprintf('PT%dH%dM%dS', $hours, $minutes, $seconds)))
        ->add(new DateInterval(sprintf('PT%dH%dM%dS', 0, 5, 0)));
}

function parseImages($validity_time, $images) {
    if (array_key_exists('question', $images)) $images['question'] = Storage::disk('questions')->temporaryUrl($images['question'], $validity_time);
    return $images;
}

class Response extends Model {
    use HasApiTokens;
    use SoftDeletes;
    use HasUuids;
    use Prunable;

    protected $primaryKey = 'uuid';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    public $fillable = [
        'exam_id',
        'student_name',
        'student_surname',
        'student_email',
    ];
    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    protected $appends = [
        'score',
        'max_score',
        'questions'
    ];

    private ?array $questionsWithAnswersCache = null;

    public function getQuestionsAttribute() {
        return array_map(function ($question) {
            return [
                'index' => $question['index'],
                'uuid' => $question['uuid'],
                'question' => $question['question'],
                'options' => $question['options'],
                'answer' => $question['answer'],
                'images' => $question['images'],
            ];
        }, $this->questions_with_answers);
    }

    public function getQuestionsWithAnswersAttribute() {
        if ($this->questionsWithAnswersCache) {
            return $this->questionsWithAnswersCache;
        }

        $questions = $this->questions()->with('question')->get();

        $user = Auth::user();
        if ($user && $user->currentAccessToken()->can('exam_result')) {
            $validity_time = now()->addMinutes(5);
        } else {
            $validity_time = getEndTime($this->start_time, $this->exam->duration, $this->exam->is_global_duration ? 1 : $this->exam->question_number);
        }

        $response = array();
        for ($i = 1; $i <= $this->exam->question_number; $i++) {
            $question = $questions->where('index', $i)->first();
            $item = [
                'index' => $i,
                'uuid' => '-',
                'question' => 'Brak pytania',
                'options' => array(),
                'answer' => null,
                'correct' => '---',
                'images' => array(),
            ];
            if (!$question) {
                $response[] = $item;
                continue;
            }
            $item = array_merge($item, [
                'uuid' => $question->question_uuid,
                'question' => $question->question->question,
                'options' => $question->question->options,
                'answer' => $question->answer,
                'correct' => $question->question->answer,
                'images' => parseImages($validity_time, $question->question->images),
            ]);

            $response[] = $item;
        }

        $this->questionsWithAnswersCache = $response;
        return $response;
    }

    private function calculateScore(): int {
        $score = 0;
        $this->questions()->with('question')->get()->each(function ($question) use (&$score) {
            if ($question->isCorrect) {
                $score++;
            }
        });

        return $score;
    }

    public function getScoreAttribute(): int {
        $this->points = $this->calculateScore();
        $this->save();

        return $this->points;
    }

    public function getMaxScoreAttribute(): int {
        return $this->exam->question_number;
    }

    public function exam(): BelongsTo {
        return $this->belongsTo(Exam::class);
    }

    public function questions(): HasMany {
        return $this->hasMany(ResponseQuestion::class, 'response_uuid', 'uuid')->orderBy('index');
    }

    public function prunable() {
        return static::onlyTrashed()->where('deleted_at', '<', now()->subDays(180));
    }
}
