<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class LoginRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'username' => [
                'required',
                'string',
                'max:50',
                'regex:/^[a-zA-Z0-9_]+$/' // Only alphanumeric and underscore
            ],
            'password' => [
                'required',
                'string',
                'min:6'
            ],
            'kodedivisi' => [
                'nullable',
                'string',
                'max:10',
                'regex:/^[A-Z0-9]+$/' // Uppercase alphanumeric for division codes
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'username.required' => 'Username is required',
            'username.regex' => 'Username can only contain letters, numbers and underscores',
            'username.max' => 'Username cannot exceed 50 characters',
            'password.required' => 'Password is required',
            'password.min' => 'Password must be at least 6 characters',
            'kodedivisi.regex' => 'Division code must be uppercase alphanumeric',
            'kodedivisi.max' => 'Division code cannot exceed 10 characters',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'kodedivisi' => 'division code',
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422)
        );
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'username' => strtolower($this->username ?? ''),
            'kodedivisi' => strtoupper($this->kodedivisi ?? ''),
        ]);
    }
}
