<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Models\MDivisi;
use App\Models\MasterUser;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Only authenticated admin users can register new users
        return $this->user() && $this->user()->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'kodedivisi' => [
                'required',
                'string',
                'max:10',
                'regex:/^[A-Z0-9]+$/',
                'exists:dbo.m_divisi,kodedivisi'
            ],
            'username' => [
                'required',
                'string',
                'max:50',
                'regex:/^[a-zA-Z0-9_]+$/',
                function ($attribute, $value, $fail) {
                    $exists = MasterUser::where('kodedivisi', strtoupper($this->kodedivisi))
                                       ->where('username', strtolower($value))
                                       ->exists();
                    if ($exists) {
                        $fail('This username already exists in the specified division.');
                    }
                }
            ],
            'nama' => [
                'required',
                'string',
                'max:100',
                'regex:/^[a-zA-Z\s\.]+$/' // Only letters, spaces, and dots
            ],
            'password' => [
                'required',
                'string',
                'min:6',
                'max:255',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/' // At least one lowercase, uppercase, and number
            ],
            'password_confirmation' => [
                'required',
                'string'
            ]
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'kodedivisi.required' => 'Division code is required',
            'kodedivisi.exists' => 'The selected division does not exist',
            'kodedivisi.regex' => 'Division code must be uppercase alphanumeric',
            'username.required' => 'Username is required',
            'username.regex' => 'Username can only contain letters, numbers and underscores',
            'username.max' => 'Username cannot exceed 50 characters',
            'nama.required' => 'Full name is required',
            'nama.regex' => 'Name can only contain letters, spaces and dots',
            'nama.max' => 'Name cannot exceed 100 characters',
            'password.required' => 'Password is required',
            'password.min' => 'Password must be at least 6 characters',
            'password.confirmed' => 'Password confirmation does not match',
            'password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, and one number',
            'password_confirmation.required' => 'Password confirmation is required',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'kodedivisi' => 'division code',
            'nama' => 'full name',
            'password_confirmation' => 'password confirmation',
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
     * Handle a failed authorization attempt.
     */
    protected function failedAuthorization()
    {
        throw new HttpResponseException(
            response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
                'errors' => ['authorization' => ['Only admin users can register new users']]
            ], 403)
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
            'nama' => ucwords(strtolower($this->nama ?? '')),
        ]);
    }
}
