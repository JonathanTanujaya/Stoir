<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAreaRequest extends FormRequest
{
    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $payload = [];
        if ($this->has('kode_area')) {
            $payload['kode_area'] = strtoupper(trim((string) $this->input('kode_area')));
        }
        if ($this->has('status')) {
            $payload['status'] = filter_var($this->input('status'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? $this->input('status');
        }
        if (! empty($payload)) {
            $this->merge($payload);
        }
    }

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
        $kodeDivisi = $this->route('kodeDivisi');
        $kodeArea = $this->route('kodeArea');
        
        return [
            'kode_area' => [
                'sometimes',
                'required',
                'string',
                'max:10',
                Rule::unique('m_area', 'kode_area')->where(function ($query) use ($kodeDivisi) {
                    return $query->where('kode_divisi', $kodeDivisi);
                })->ignore($kodeArea, 'kode_area'),
            ],
            'area' => 'sometimes|required|string|max:50',
            'status' => 'sometimes|boolean',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'kode_area.required' => 'Kode area wajib diisi.',
            'kode_area.string' => 'Kode area harus berupa string.',
            'kode_area.max' => 'Kode area maksimal 10 karakter.',
            'kode_area.unique' => 'Kode area sudah digunakan di divisi ini.',
            'area.required' => 'Nama area wajib diisi.',
            'area.string' => 'Nama area harus berupa string.',
            'area.max' => 'Nama area maksimal 50 karakter.',
            'status.boolean' => 'Status harus berupa boolean.',
        ];
    }
}
