<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Response;
use App\Services\Admin\ResponseService;
use Illuminate\Http\Request;

class ResponseController extends Controller
{
    public function __construct(private ResponseService $service) {
    }

    public function index(Exam $exam, Request $request) {
        $responses = $this->service->getResponses($exam, $request->query('trashed') === 'true');

        return response()->json([
            'success' => true,
            'responses' => $responses
        ]);
    }
    public function list(Exam $exam) {
        $list = $this->service->getList($exam);

        return response()->json([
            'success' => true,
            'list' => $list
        ]);
    }

    public function remove(Exam $_, Response $response) {
        $this->service->removeResponse($response);

        return response()->json([
            'success' => true,
            'message' => 'Response removed'
        ]);
    }
    public function restore(Exam $_, Response $response) {
        $this->service->restoreResponse($response);

        return response()->json([
            'success' => true,
            'message' => 'Response restored'
        ]);
    }
}
