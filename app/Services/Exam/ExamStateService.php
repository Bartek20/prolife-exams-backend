<?php

namespace App\Services\Exam;

use App\Models\Exam;
use App\Models\Response;
use App\Models\Statistic;

class ExamStateService {
    public function getConfig($code) {
        $exam = Exam::whereRaw('access_code = ? COLLATE BINARY', [$code])->first();
        if (!$exam) {
            return null;
        }
        return [
            'id' => $exam->id,
            'access_code' => $exam->access_code,
            'name' => $exam->name,
            'start_time' => $exam->start_time,
            'end_time' => $exam->end_time,
            'duration' => $exam->duration,
            'question_number' => $exam->question_number,
            'passing_score' => $exam->passing_score,
            'can_go_back' => $exam->can_go_back,
            'is_global_duration' => $exam->is_global_duration,
        ];
    }

    public function getState($response) {
        if (!$response) return null;

        $config = $this->getConfig($response->exam->access_code);
        $state = [
            'uuid' => $response->uuid,
            'start_time' => $response->start_time,
            'end_time' => $response->end_time,
            'questions' => $response->questions,
        ];

        return [
            'config' => $config,
            'state' => $state,
        ];
    }

    public function createResponse($exam, $student) {
        $student = array_merge([
            'name' => 'Kursant',
            'surname' => 'PROLIFE',
            'email' => 'kursant@szkolenia-prolife.pl'
        ], array_filter($student, fn ($v) => $v !== null));

        $response = Response::create([
            'exam_id' => $exam['id'],
            'student_name' => $student['name'],
            'student_surname' => $student['surname'],
            'student_email' => $student['email'],

        ]);

        $this->updateStatistics($exam['id']);

        return $response;
    }
    public function finishResponse($response) {
        $response->end_time = now();
        $response->status = 'finished';
        $response->save();

        $this->updateExamToken($response->currentAccessToken());

        $this->updateStatistics($response->exam_id, 'finish');
    }

    private function updateStatistics($exam_id, $mode = 'create') {
        $stats = Statistic::firstOrNew([
            'exam_id' => $exam_id,
            'date' => now()->format('Y-m-d'),
        ], [
            'created' => 0,
            'completed' => 0,
        ]);
        if ($mode == 'create') {
            $stats->created++;
        }
        else {
            $stats->completed++;
        }
        $stats->save();
    }
    private function updateExamToken($token) {
        $token->abilities = ['exam_result'];
        $token->save();
    }
}
