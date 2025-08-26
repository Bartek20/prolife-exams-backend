<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdminUpdateExamRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'access_code' => 'prohibits',
            'start_time' => [
                'required',
                Rule::date()->after(now())
            ],
            'end_time' => 'nullable|date|after:start_time',
            'duration' => 'required|date_format:H:i:s',
            'question_number' => 'required|integer|min:1|max:250',
            'passing_score' => 'required|integer|min:0|lte:question_number',
            'can_go_back' => 'required|boolean',
            'is_global_duration' => 'required|boolean',
            'show_results' => 'required|boolean',
        ];
    }
}
