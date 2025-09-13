<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBarangRequest extends FormRequest
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
        $kodeBarang = $this->route('kodeBarang');
        
        return [
            'nama_barang' => 'sometimes|required|string|max:100',
            'kode_kategori' => [
                'sometimes',
                'required',
                'string',
                'max:10',
                Rule::exists('m_kategori')->where(function ($query) use ($kodeDivisi) {
                    return $query->where('kode_divisi', $kodeDivisi);
                })
            ],
            'harga_list' => 'sometimes|nullable|numeric|min:0|max:999999999.99',
            'harga_jual' => 'sometimes|nullable|numeric|min:0|max:999999999.99',
            'satuan' => 'sometimes|nullable|string|max:10',
            'disc1' => 'sometimes|nullable|numeric|min:0|max:100',
            'disc2' => 'sometimes|nullable|numeric|min:0|max:100',
            'merk' => 'sometimes|nullable|string|max:50',
            'barcode' => [
                'sometimes',
                'nullable',
                'string',
                'max:50',
                Rule::unique('m_barang', 'barcode')->ignore($kodeBarang, 'kode_barang')
                    ->where('kode_divisi', $kodeDivisi)
            ],
            'status' => 'sometimes|nullable|boolean',
            'lokasi' => 'sometimes|nullable|string|max:50',
            'stok_min' => 'sometimes|nullable|integer|min:0|max:999999'
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
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
        if ($this->has('kode_kategori')) {
            $this->merge([
                'kode_kategori' => strtoupper($this->kode_kategori)
            ]);
        }
    }
}
