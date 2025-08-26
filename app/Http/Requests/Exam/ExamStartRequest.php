<?php

namespace App\Http\Requests\Exam;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class ExamStartRequest extends FormRequest {
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array {
        if (!$this->access_code) return [
            'access_code' => 'required|string',
        ];

        if (in_array($this->access_code, ['DEMO', 'EgzaminProbny'])) return [];

        return [
            'student_name' => 'required|string',
            'student_surname' => 'required|string',
            'student_email' => 'required|email',
        ];
    }
}
