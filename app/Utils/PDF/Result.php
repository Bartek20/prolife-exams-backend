<?php

namespace App\Utils\PDF;

class Result extends PDFBuilder {

    public function __construct(private $data) {
        parent::__construct();
    }

    private function loadImages(): void {
        foreach ($this->data['questions'] as &$question) {
            if (array_key_exists('question', $question['images'])) {
                preg_match('/images\/(.*)\?/m', $question['images']['question'], $matches, PREG_OFFSET_CAPTURE);
                $uuid = $matches[1][0];
                $question['images']['question'] = $uuid;
                $this->images[$uuid] = storage_path('app/questions/' . $uuid);
            }
        }
    }

    protected function getFullHTML(): string {
        $this->loadImages();
        return view('pdf.result', [
            'student'=> $this->data['student'],
            'exam' => $this->data['exam'],
            'score' => $this->data['score'],
            'questions' => $this->data['questions'],
        ])->render();
    }
}
