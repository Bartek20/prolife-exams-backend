<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GenerateCertificateRequest extends FormRequest
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
            'certificate_type' => [
                'required',
                Rule::in(['StopTheBleed'])
            ],
            'student_name' => 'required|string',
            'instructors_name' => 'required|string',
            'certificate_number' => 'required|string',
            'certificate_date' => 'required|date',
        ];
    }
}
