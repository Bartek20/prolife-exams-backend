<?php

namespace App\Services\Admin;

use App\Http\Requests\Admin\CreateQuestionRequest;
use App\Models\Question;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class QuestionService {
    public function getQuestions() {
        return Question::select(['uuid', 'question'])->get();
    }
    public function getQuestion(Question $question) {
        $resp = $question->toArray();
        if (array_key_exists('question', $resp['images'])) {
            $resp['images']['question'] = Storage::disk('questions')->temporaryUrl($resp['images']['question'], now()->addMinutes(5));
        }
        return $resp;
    }

    public function create(CreateQuestionRequest $request) {
        if ($request->hasFile('image')) $uuid = $this->putImage($request->file('image'));
        $data = $request->validated();
        if (isset($uuid)) {
            $data['images'] = [
                'question' => $uuid,
            ];
        }
        $this->createQuestion($data);
    }

    private function putImage(UploadedFile $image) {
        $uuid = Str::uuid();
        Storage::disk('questions')->put($uuid, $image->getContent());

        return $uuid;
    }
    private function createQuestion($data) {
        Question::create($data);
    }
}
