<?php

namespace App\Http\Controllers\Exam;

use App\Http\Controllers\Controller;
use App\Services\Exam\ExamResultService;
use App\Utils\PDF\Result;
use Illuminate\Http\Request;

class ResultController extends Controller
{
    public function __construct(private readonly ExamResultService $service) {
    }

    private function getData($request): array {
        $exam = $request->attributes->get('exam');
        $response = $request->attributes->get('response');

        return $this->service->getResult($exam, $response);
    }

    public function getResult(Request $request) {

        $result = $this->getData($request);

        return response()->json($result);
    }

    public function getPDF(Request $request) {
        $result = $this->getData($request);

        $resultPDF = new Result($result);
        $resultPDF->getFile('Wyniki Egzaminu.pdf');
    }
}
