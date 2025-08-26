<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $isResults = $request->attributes->has('response') && $request->attributes->get('response')->status != 'in_progress';
        $isAdmin = auth()->check() && auth()->user()->role == 'teacher';

        $question = $this->question;

        return [
            'index' => (int) $this->index,
            'uuid' => $question->uuid,
            'question' => $question->question,
            'options' => $question->options,
            'answer' => $this->answer,
            'correct' => $this->when($isResults || $isAdmin, $question->correct),
            'images' => $question->images,
            'generated_at' => $this->generated_at ?? now()->setMilliseconds(0)
        ];
    }
}
