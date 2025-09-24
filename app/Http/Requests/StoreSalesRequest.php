<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSalesRequest extends FormRequest
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
            'kode_sales' => [
                'required',
                'string',
                'max:15',
                'regex:/^[A-Z0-9\-_]+$/',
                Rule::unique('m_sales', 'kode_sales')
            ],
            'nama_sales' => 'required|string|max:50',
            'kode_area' => [
                'nullable',
                'string',
                'max:5',
                Rule::exists('m_area', 'kode_area')
            ],
            'alamat' => 'nullable|string|max:500',
            'no_hp' => 'nullable|string|max:20|regex:/^[\d\-\+\(\)\s]+$/',
            'target' => 'nullable|numeric|min:0|max:999999999.99',
            'status' => 'nullable|boolean'
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'kode_sales.required' => 'Kode sales wajib diisi',
            'kode_sales.string' => 'Kode sales harus berupa teks',
            'kode_sales.max' => 'Kode sales maksimal 15 karakter',
            'kode_sales.regex' => 'Kode sales hanya boleh mengandung huruf kapital, angka, tanda minus, dan underscore',
            'kode_sales.unique' => 'Kode sales sudah digunakan dalam divisi ini',
            'nama_sales.required' => 'Nama sales wajib diisi',
            'nama_sales.string' => 'Nama sales harus berupa teks',
            'nama_sales.max' => 'Nama sales maksimal 50 karakter',
            'kode_area.string' => 'Kode area harus berupa teks',
            'kode_area.max' => 'Kode area maksimal 5 karakter',
            'kode_area.exists' => 'Area tidak ditemukan dalam divisi ini',
            'alamat.string' => 'Alamat harus berupa teks',
            'alamat.max' => 'Alamat maksimal 500 karakter',
            'no_hp.string' => 'No HP harus berupa teks',
            'no_hp.max' => 'No HP maksimal 20 karakter',
            'no_hp.regex' => 'Format No HP tidak valid. Gunakan angka, tanda minus, plus, kurung, dan spasi',
            'target.numeric' => 'Target harus berupa angka',
            'target.min' => 'Target tidak boleh kurang dari 0',
            'target.max' => 'Target tidak boleh lebih dari 999,999,999.99',
            'status.boolean' => 'Status harus berupa true atau false'
        ];
    }

    /**
     * Get custom attribute names for validation errors.
     */
    public function attributes(): array
    {
        return [
            'kode_sales' => 'kode sales',
            'nama_sales' => 'nama sales',
            'kode_area' => 'kode area',
            'alamat' => 'alamat',
            'no_hp' => 'no HP',
            'target' => 'target',
            'status' => 'status'
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'kode_sales' => isset($this->kode_sales) ? strtoupper((string) $this->kode_sales) : $this->kode_sales,
            'kode_area' => isset($this->kode_area) ? strtoupper((string) $this->kode_area) : $this->kode_area,
            'status' => isset($this->status) ? filter_var($this->status, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? $this->status : $this->status,
        ]);
    }
}
