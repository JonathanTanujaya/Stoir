<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSupplierRequest extends FormRequest
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
            'kode_supplier' => [
                'required',
                'string',
                'max:15',
                'regex:/^[A-Z0-9\-_]+$/',
                Rule::unique('m_supplier', 'kode_supplier')
            ],
            'nama_supplier' => 'required|string|max:50',
            'alamat' => 'nullable|string|max:100',
            'telp' => 'nullable|string|max:20|regex:/^[\d\-\+\(\)\s]+$/',
            'contact' => 'nullable|string|max:50',
            'status' => 'nullable|boolean'
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'kode_supplier.required' => 'Kode supplier wajib diisi',
            'kode_supplier.string' => 'Kode supplier harus berupa teks',
            'kode_supplier.max' => 'Kode supplier maksimal 15 karakter',
            'kode_supplier.regex' => 'Kode supplier hanya boleh mengandung huruf kapital, angka, tanda minus, dan underscore',
            'kode_supplier.unique' => 'Kode supplier sudah digunakan dalam divisi ini',
            'nama_supplier.required' => 'Nama supplier wajib diisi',
            'nama_supplier.string' => 'Nama supplier harus berupa teks',
            'nama_supplier.max' => 'Nama supplier maksimal 50 karakter',
            'alamat.string' => 'Alamat harus berupa teks',
            'alamat.max' => 'Alamat maksimal 100 karakter',
            'telp.string' => 'Telepon harus berupa teks',
            'telp.max' => 'Telepon maksimal 20 karakter',
            'telp.regex' => 'Format telepon tidak valid. Gunakan angka, tanda minus, plus, kurung, dan spasi',
            'contact.string' => 'Contact harus berupa teks',
            'contact.max' => 'Contact maksimal 50 karakter',
            'status.boolean' => 'Status harus berupa true atau false'
        ];
    }

    /**
     * Get custom attribute names for validation errors.
     */
    public function attributes(): array
    {
        return [
            'kode_supplier' => 'kode supplier',
            'nama_supplier' => 'nama supplier',
            'alamat' => 'alamat',
            'telp' => 'telepon',
            'contact' => 'contact',
            'status' => 'status'
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $status = $this->status;
        if ($status !== null) {
            // Normalize various truthy/falsey representations including legacy 'A'/'N'
            $normalized = match (strtoupper((string) $status)) {
                'A', '1', 'TRUE', 'YES', 'Y' => true,
                'N', '0', 'FALSE', 'NO' => false,
                default => $status,
            };
        } else {
            $normalized = true; // default to active when not provided
        }

        $this->merge([
            'kode_supplier' => strtoupper($this->kode_supplier ?? ''),
            'status' => $normalized,
        ]);
    }
}
