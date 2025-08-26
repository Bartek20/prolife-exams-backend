<?php

namespace App\Services\Exam;

class ExamResultService {
    public function getResult($exam, $response) {
        $student = [
            'name' => $response->student_name,
            'surname' => $response->student_surname,
            'email' => $response->student_email,
        ];

        $examDetails = [
            'name' => $exam->name,
            'access_code' => $exam->access_code,
            'isDEMO' => in_array($exam->id, [1, 2]),
            'uuid' => $response->uuid,
            'start_time' => $response->start_time,
            'end_time' => $response->end_time,
        ];

        $scoreDetails = [
            'exam' => $response->score,
            'max' => $response->max_score,
            'passing' => $exam->passing_score,
        ];

        $questions = $response->questions_with_answers;

        return [
            'student' => $student,
            'exam' => $examDetails,
            'score' => $scoreDetails,
            'questions' => $questions,
        ];
    }
}
