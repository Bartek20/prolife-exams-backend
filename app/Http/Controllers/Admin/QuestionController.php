<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreateQuestionRequest;
use App\Models\Question;
use App\Services\Admin\QuestionService;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    public function __construct(private QuestionService $questionService) {
    }

    public function index() {
        $questions = $this->questionService->getQuestions();

        return response()->json([
            'success' => true,
            'questions' => $questions
        ], 200);
    }

    public function show(Question $question) {
        $response = $this->questionService->getQuestion($question);

        return response()->json([
            'success' => true,
            'question' => $response
        ]);
    }

    public function store(CreateQuestionRequest $request) {
        $this->questionService->create($request);

        return response()->json([
            'success' => true,
            'message' => 'Question created'
        ]);
    }
}
