<?php

namespace App\Services\Admin;

use App\Models\Exam;
use Illuminate\Support\Facades\DB;

class ExamService {
    public function getExams() {
        $exams = Exam::select('id', 'name', 'start_time', 'end_time')->withCount('responses as responses')->get();
        return $exams->map(function ($exam) {
            return array(
                'id' => $exam->id,
                'name' => $exam->name,
                'start_time' => $exam->start_time,
                'end_time' => $exam->end_time,
                'status' => $exam->status,
                'responses' => $exam->responses
            );
        });
    }
    public function getExam($id) {
        return Exam::withCount([
            'responses as responses',
            'responses as trashed_responses' => function ($query) {
                $query->onlyTrashed();
            }
        ])->where('id', $id)->first();
    }

    public function createExam($config) {
        $exam = Exam::create($config);

        return $exam->id;
    }
    public function updateExam($id, $config) {
//        TODO
    }
    public function deleteExam($exam) {
        $response_uuids = $exam->responses()->pluck('uuid');
        DB::table('personal_access_tokens')->where('tokenable_type', 'App\Models\Response')->whereIn('tokenable_id', $response_uuids)->delete();

        $exam->stats()->delete();
        $exam->responses()->forceDelete();

        $exam->delete();
    }
}
