<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAreaRequest extends FormRequest
{
    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'kode_area' => isset($this->kode_area) ? strtoupper(trim((string) $this->kode_area)) : $this->kode_area,
            'status' => isset($this->status) ? filter_var($this->status, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? $this->status : $this->status,
        ]);
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
        
        return [
            'kode_area' => [
                'required',
                'string',
                'max:10',
                Rule::unique('m_area', 'kode_area')->where(function ($query) use ($kodeDivisi) {
                    return $query->where('kode_divisi', $kodeDivisi);
                }),
            ],
            'area' => 'required|string|max:50',
            'status' => 'boolean',
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
