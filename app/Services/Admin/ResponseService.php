<?php

namespace App\Services\Admin;

use App\Models\Exam;
use App\Models\Response;

class ResponseService {
    public function getResponses(Exam $exam, bool $trashed = false) {
        $responses = $exam->responses()->when($trashed, function ($query) {
            return $query->onlyTrashed();
        })->select(['uuid', 'student_name', 'student_surname', 'start_time', 'end_time', 'points', 'status', 'exam_id'])->get();
        return $responses->map(function ($response) use ($exam) {
            return array(
                'uuid' => $response->uuid,
                'student_name' => $response->student_name,
                'student_surname' => $response->student_surname,
                'start_date' => $response->start_time,
                'end_date' => $response->end_time ?? null,
                'score' => $response->score,
                'max_score' => $response->max_score,
                'passed'=> $response->score >= $exam->passing_score,
                'finished' => $response->status != 'in_progress',
                'deleted_at' => $response->deleted_at ?? null,
            );
        });
    }
    public function getList(Exam $exam) {
        $responses = $exam->responses()->select('id', 'uuid', 'student_name', 'student_surname')->whereNotNull('end_time')->get();
        return $responses->map(function ($response) {
            return array(
                'uuid' => $response->uuid,
                'student_name' => $response->student_name,
                'student_surname' => $response->student_surname,
            );
        });
    }

    public function removeResponse(Response $response) {
        if ($response->trashed()) {
            $response->forceDelete();
        } else {
            $response->delete();
        }
    }
    public function restoreResponse(Response $response) {
        if (!$response->trashed()) return;
        $response->restore();
    }
}
