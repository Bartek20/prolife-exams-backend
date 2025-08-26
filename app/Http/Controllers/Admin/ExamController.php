<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminCreateExamRequest;
use App\Http\Requests\Admin\AdminUpdateExamRequest;
use App\Models\Exam;
use App\Services\Admin\ExamService;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    public function __construct(private ExamService $examService) {
    }

    public function index() {
        return response()->json([
            'success' => true,
            'exams' => $this->examService->getExams()
        ]);
    }

    public function show(Exam $exam) {
        return response()->json([
            'success' => true,
            'exam' => $this->examService->getExam($exam->id)
        ]);
    }

    public function store(AdminCreateExamRequest $request) {
        return response()->json([
            'success' => true,
            'message' => 'Exam created',
            'exam' => $this->examService->createExam($request->validated())
        ]);
    }

    public function update(AdminUpdateExamRequest $request, Exam $exam) {
        $this->examService->updateExam($exam->id, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Exam updated'
        ]);
    }

    public function destroy(Exam $exam) {
        $this->examService->deleteExam($exam);

        return response()->json([
            'success' => true,
            'message' => 'Exam deleted'
        ]);
    }
}
