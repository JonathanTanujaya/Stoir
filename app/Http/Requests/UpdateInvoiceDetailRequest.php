<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateInvoiceDetailRequest extends FormRequest
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
            'kode_barang' => [
                'sometimes',
                'string',
                'max:50',
                Rule::exists('m_barang', 'kode_barang')
            ],
            'qty_supply' => 'sometimes|integer|min:1',
            'harga_jual' => 'sometimes|numeric|min:0',
            'jenis' => 'sometimes|nullable|string|max:50',
            'diskon1' => 'sometimes|nullable|numeric|min:0|max:100',
            'diskon2' => 'sometimes|nullable|numeric|min:0|max:100',
            'harga_nett' => 'sometimes|numeric|min:0',
            'status' => 'sometimes|nullable|string|max:50',
        ];
    }

    /**
     * Get custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'kode_barang.string' => 'Kode barang harus berupa teks.',
            'kode_barang.max' => 'Kode barang maksimal 50 karakter.',
            'kode_barang.exists' => 'Kode barang tidak ditemukan dalam divisi ini.',
            'qty_supply.integer' => 'Quantity supply harus berupa angka bulat.',
            'qty_supply.min' => 'Quantity supply minimal 1.',
            'harga_jual.numeric' => 'Harga jual harus berupa angka.',
            'harga_jual.min' => 'Harga jual tidak boleh negatif.',
            'jenis.string' => 'Jenis harus berupa teks.',
            'jenis.max' => 'Jenis maksimal 50 karakter.',
            'diskon1.numeric' => 'Diskon 1 harus berupa angka.',
            'diskon1.min' => 'Diskon 1 tidak boleh negatif.',
            'diskon1.max' => 'Diskon 1 maksimal 100%.',
            'diskon2.numeric' => 'Diskon 2 harus berupa angka.',
            'diskon2.min' => 'Diskon 2 tidak boleh negatif.',
            'diskon2.max' => 'Diskon 2 maksimal 100%.',
            'harga_nett.numeric' => 'Harga nett harus berupa angka.',
            'harga_nett.min' => 'Harga nett tidak boleh negatif.',
            'status.string' => 'Status harus berupa teks.',
            'status.max' => 'Status maksimal 50 karakter.',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('kode_barang')) {
            $this->merge([
                'kode_barang' => strtoupper($this->kode_barang)
            ]);
        }
    }
}
