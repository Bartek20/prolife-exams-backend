<?php

namespace App\Http\Controllers\Exam;

use App\Http\Controllers\Controller;
use App\Http\Requests\Exam\ExamAnswerRequest;
use App\Services\Exam\ExamQuestionService;
use Illuminate\Http\Request;

class QuestionController extends Controller {
    public function __construct(private ExamQuestionService $service) {
    }

    public function question(Request $request, $idx) {
        $response = $request->attributes->get('response');
        $this->service->setupService($response, $idx);

        $validationError = $this->service->validateRequest();
        if ($validationError) return $validationError;

        $question = $this->service->getQuestion();

        return response()->json([
            'success' => true,
            'question' => $question,
        ]);
    }

    public function answer(ExamAnswerRequest $request, $idx) {
        $response = $request->attributes->get('response');
        $this->service->setupService($response, $idx);

        $validationError = $this->service->validateRequest();
        if ($validationError) return $validationError;

        $question = $response->questions()->where('index', $idx)->select('question_uuid', 'generated_at')->first();
        if ($request->uuid != $question->question_uuid) return response()->json([
            'success' => false,
            'message' => 'Invalid question uuid',
        ], 400);

        $this->service->setAnswer($request->answer);

        return response()->json([
            'success' => true,
            'message' => 'Answer saved',
        ]);
    }
}
