<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBarangRequest extends FormRequest
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
        $kodeDivisi = $this->route('kodeDivisi');
        
        return [
            'kode_barang' => [
                'required',
                'string',
                'max:20',
                'regex:/^[A-Z0-9\-_]+$/',
                Rule::unique('m_barang')->where(function ($query) use ($kodeDivisi) {
                    return $query->where('kode_divisi', $kodeDivisi);
                })
            ],
            'nama_barang' => 'required|string|max:100',
            'kode_kategori' => [
                'required',
                'string',
                'max:10',
                Rule::exists('m_kategori')->where(function ($query) use ($kodeDivisi) {
                    return $query->where('kode_divisi', $kodeDivisi);
                })
            ],
            'harga_list' => 'nullable|numeric|min:0|max:999999999.99',
            'harga_jual' => 'nullable|numeric|min:0|max:999999999.99',
            'satuan' => 'nullable|string|max:10',
            'disc1' => 'nullable|numeric|min:0|max:100',
            'disc2' => 'nullable|numeric|min:0|max:100',
            'merk' => 'nullable|string|max:50',
            'barcode' => 'nullable|string|max:50|unique:m_barang,barcode',
            'status' => 'nullable|boolean',
            'lokasi' => 'nullable|string|max:50',
            'stok_min' => 'nullable|integer|min:0|max:999999'
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'kode_barang' => 'kode barang',
            'nama_barang' => 'nama barang',
            'kode_kategori' => 'kategori',
            'harga_list' => 'harga list',
            'harga_jual' => 'harga jual',
            'satuan' => 'satuan',
            'disc1' => 'diskon 1',
            'disc2' => 'diskon 2',
            'merk' => 'merk',
            'barcode' => 'barcode',
            'status' => 'status',
            'lokasi' => 'lokasi',
            'stok_min' => 'stok minimum'
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'kode_barang.regex' => 'Kode barang hanya boleh mengandung huruf besar, angka, tanda minus, dan underscore.',
            'kode_barang.unique' => 'Kode barang sudah digunakan dalam divisi ini.',
            'barcode.unique' => 'Barcode sudah digunakan.',
            'kode_kategori.exists' => 'Kategori tidak ditemukan dalam divisi ini.',
            'disc1.max' => 'Diskon 1 tidak boleh lebih dari 100%.',
            'disc2.max' => 'Diskon 2 tidak boleh lebih dari 100%.'
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Auto-set default values
        $this->merge([
            'status' => $this->status ?? true,
            'kode_barang' => strtoupper($this->kode_barang),
            'kode_kategori' => strtoupper($this->kode_kategori)
        ]);
    }
}
