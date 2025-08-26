<?php

namespace App\Http\Controllers\Exam;

use App\Http\Controllers\Controller;
use App\Http\Requests\Exam\ExamReportRequest;
use App\Services\Exam\ExamReportService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct(private ExamReportService $service) {
    }

    private function getExamID($request) {
        return $request->attributes->get('response')->exam_id;
    }

    public function public(Request $request) {
        $key = $this->service->getPublicKey($this->getExamID($request));

        return response($key, 200);
    }

    public function report(ExamReportRequest $request) {
        $signature = $this->service->handleReport($request);

        return response($signature, 200);
    }
}
