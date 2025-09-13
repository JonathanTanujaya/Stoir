<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDivisiRequest extends FormRequest
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
            'kode_divisi' => 'required|string|max:5|unique:m_divisi,kode_divisi',
            'nama_divisi' => 'required|string|max:50',
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
            'kode_divisi.required' => 'Kode divisi wajib diisi.',
            'kode_divisi.string' => 'Kode divisi harus berupa string.',
            'kode_divisi.max' => 'Kode divisi maksimal 5 karakter.',
            'kode_divisi.unique' => 'Kode divisi sudah digunakan.',
            'nama_divisi.required' => 'Nama divisi wajib diisi.',
            'nama_divisi.string' => 'Nama divisi harus berupa string.',
            'nama_divisi.max' => 'Nama divisi maksimal 50 karakter.',
        ];
    }
}
