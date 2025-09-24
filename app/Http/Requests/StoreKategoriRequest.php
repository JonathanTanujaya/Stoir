<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreKategoriRequest extends FormRequest
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
            'kode_kategori' => [
                'required',
                'string',
                'max:10',
                Rule::unique('m_kategori', 'kode_kategori'),
            ],
            'kategori' => 'required|string|max:50',
            'status' => 'boolean',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $kodeKategori = $this->input('kode_kategori');
        $status = $this->input('status');

        $this->merge([
            'kode_kategori' => is_string($kodeKategori) ? strtoupper(trim($kodeKategori)) : $kodeKategori,
            'status' => $this->boolean('status'),
        ]);
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'kode_kategori.required' => 'Kode kategori wajib diisi.',
            'kode_kategori.string' => 'Kode kategori harus berupa string.',
            'kode_kategori.max' => 'Kode kategori maksimal 10 karakter.',
            'kode_kategori.unique' => 'Kode kategori sudah digunakan di divisi ini.',
            'kategori.required' => 'Nama kategori wajib diisi.',
            'kategori.string' => 'Nama kategori harus berupa string.',
            'kategori.max' => 'Nama kategori maksimal 50 karakter.',
            'status.boolean' => 'Status harus berupa boolean.',
        ];
    }
}
