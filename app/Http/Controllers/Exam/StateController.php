<?php

namespace App\Http\Controllers\Exam;

use App\Http\Controllers\Controller;
use App\Http\Requests\Exam\ExamRestoreRequest;
use App\Http\Requests\Exam\ExamStartRequest;
use App\Models\Response;
use App\Services\Exam\ExamStateService;
use Illuminate\Http\Request;

class StateController extends Controller
{
    public function __construct(private ExamStateService $service) {
    }

    public function config($code) {
        $config = $this->service->getConfig($code);

        if (!$config) {
            return response()->json([
                'success' => false,
                'error' => 'Provided access code does not exist.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'config' => $config,
        ]);
    }

    public function state(Request $request) {
        $state = $this->service->getState($request->attributes->get('response'));
        return response()->json(array_merge([
            'success' => true,
        ], $state));
    }

    public function start(ExamStartRequest $request) {
        $exam = $this->service->getConfig($request->access_code);

        if (!$exam) return response()->json([
            'success' => false,
            'error' => 'Provided access code does not exist.',
        ], 404);

        if ($exam['start_time'] > now()) return response()->json([
            'success' => false,
            'error' => 'Exam haven\'t started yet.',
        ], 401);

        if ($exam['end_time'] && $exam['end_time'] <= now()) return response()->json([
            'success' => false,
            'error' => 'Exam has ended.',
        ], 401);

        $response = $this->service->createResponse($exam, [
            'name' => $request->name,
            'surname' => $request->surname,
            'email' => $request->email
        ]);
        $token = $response->createToken('exam_token', ['exam_state', 'exam_fill']);

        return response()->json([
            'success' => true,
            'message' => 'Exam has started.',
            'uuid' => $response->uuid,
            'token' => explode('|', $token->plainTextToken)[1],
        ]);
    }
    public function finish(Request $request) {
        $response = $request->attributes->get('response');

        $this->service->finishResponse($response);

        return response()->json([
            'success' => true,
            'message' => 'Exam finished.',
        ]);
    }
    public function restore(ExamRestoreRequest $request) {
        $response = Response::where('uuid', $request->uuid)->first();
        if (!$response) return response()->json([
            'success' => false,
            'error' => 'Response not found.',
        ], 404);

        if ($response->student_name    !== $request->student_name ||
            $response->student_surname !== $request->student_surname ||
            $response->student_email   !== $request->student_email) {
            return response()->json([
                'success' => false,
                'error' => 'Invalid response credentials.',
            ], 401);
        }

        $token = $response->createToken('exam_token', $response->status == 'in_progress' ? ['exam_state', 'exam_fill'] : ['exam_result']);
        return response()->json([
            'success' => true,
            'message' => 'Exam restored successfully.',
            'token' => explode('|', $token->plainTextToken)[1],
        ]);
    }
}
