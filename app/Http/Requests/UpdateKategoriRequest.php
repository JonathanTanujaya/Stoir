<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateKategoriRequest extends FormRequest
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
        $kodeKategori = $this->route('kodeKategori');
        
        return [
            'kode_kategori' => [
                'sometimes',
                'required',
                'string',
                'max:10',
                Rule::unique('m_kategori', 'kode_kategori')->ignore($kodeKategori, 'kode_kategori'),
            ],
            'kategori' => 'sometimes|required|string|max:50',
            'status' => 'sometimes|boolean',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $payload = [];
        if ($this->has('kode_kategori')) {
            $payload['kode_kategori'] = is_string($this->input('kode_kategori'))
                ? strtoupper(trim($this->input('kode_kategori')))
                : $this->input('kode_kategori');
        }
        if ($this->has('status')) {
            $payload['status'] = $this->boolean('status');
        }

        if (! empty($payload)) {
            $this->merge($payload);
        }
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
