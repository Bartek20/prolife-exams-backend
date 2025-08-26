<?php

namespace App\Services\Exam;

use App\Http\Resources\QuestionResource;
use App\Models\Question;
use App\Models\ResponseQuestion;

class ExamQuestionService {
    private $response;
    private $idx;

    public function setupService($response, $idx) {
        $this->response = $response;
        $this->idx = $idx;
    }
    public function validateRequest() {
        if (!$this->response) throw new \Exception('Setup service before calling validateRequest()');

        if ($this->response->status !== 'in_progress') return response()->json([
            'success' => false,
            'message' => 'Exam has ended',
        ], 403);
        $exam = $this->response->exam;
        $canModify = function ($q) use ($exam) {
            if (!$exam->is_global_duaration) {
                [$hours, $minutes, $seconds] = explode(':', $exam->duration);
                $endTime = $q->generated_at
                    ->addHours((int) $hours)
                    ->addMinutes((int) $minutes)
                    ->addSeconds((int) $seconds)
                    ->addSeconds(5); // Allow 5 seconds buffer
                if (now()->greaterThan($endTime)) return response()->json([
                    'success' => false,
                    'message' => 'Question time has ended',
                ], 400);
            }
            return true;
        };
        if ($this->idx < 1 || $this->idx > $exam->question_number) return response()->json([
            'success' => false,
            'message' => 'Question index out of bounds',
        ], 400);
        if (!$exam->can_go_back) {
            $questions = $this->response->questions()->whereIn('index', [$this->idx, $this->idx - 1])->select('index', 'answer', 'generated_at')->get();
            $previousQuestion = $questions->where('index', $this->idx - 1)->first();
            $currentQuestion = $questions->where('index', $this->idx)->first();

            $currentAnswer = $currentQuestion->answer ?? null;
            $previousAnswer = $previousQuestion->answer ?? null;

            if ($this->idx != 1 && is_null($previousAnswer) && $canModify($previousQuestion) === true) return response()->json([
                'success' => false,
                'message' => 'Answer previous question first',
            ], 400);
            if (request()->isMethod('POST')) {
                if (!is_null($currentAnswer)) return response()->json([
                    'success' => false,
                    'message' => 'Question already answered',
                ], 400);
                $c = $canModify($currentQuestion);
                if ($c !== true) {
                    return $c; // Return the error response if not true
                }
            }
        }
        return false;
    }
    public function getQuestion() {
        if (!$this->response) throw new \Exception('Setup service before calling getQuestion()');

        $question = $this->response->questions()->where('index', $this->idx)->first();
        if ($question) return new QuestionResource($question);

        $random = $this->randomizeQuestion();
        $question = ResponseQuestion::create([
            'response_uuid' => $this->response->uuid,
            'index' => $this->idx,
            'question_uuid' => $random,
        ]);

        return new QuestionResource($question);
    }
    public function setAnswer($answer) {
        if (!$this->response) throw new \Exception('Setup service before calling setAnswer()');

        $question = $this->response->questions()->where('index', $this->idx)->first();
        $question->answer = $answer;
        $question->save();
    }

    private function getUUIDs() {
        return $this->response->questions()->pluck('question_uuid');
    }
    private function randomizeQuestion() {
        $uuids = Question::whereNotIn('uuid', $this->getUUIDs())->pluck('uuid');
        return $uuids->random();
    }
}
