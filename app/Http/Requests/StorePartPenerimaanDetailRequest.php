<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePartPenerimaanDetailRequest extends FormRequest
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
                'required',
                'string',
                'max:30',
                Rule::exists('master_barang', 'kode_barang')
            ],
            'qty_supply' => 'required|integer|min:1',
            'harga' => 'required|numeric|min:0',
            'diskon1' => 'nullable|numeric|min:0|max:100',
            'diskon2' => 'nullable|numeric|min:0|max:100',
            'harga_nett' => 'required|numeric|min:0',
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
            'kode_barang.required' => 'Kode barang harus diisi.',
            'kode_barang.string' => 'Kode barang harus berupa teks.',
            'kode_barang.max' => 'Kode barang maksimal 30 karakter.',
            'kode_barang.exists' => 'Kode barang tidak ditemukan.',
            'qty_supply.required' => 'Quantity supply harus diisi.',
            'qty_supply.integer' => 'Quantity supply harus berupa angka bulat.',
            'qty_supply.min' => 'Quantity supply minimal 1.',
            'harga.required' => 'Harga harus diisi.',
            'harga.numeric' => 'Harga harus berupa angka.',
            'harga.min' => 'Harga tidak boleh negatif.',
            'diskon1.numeric' => 'Diskon 1 harus berupa angka.',
            'diskon1.min' => 'Diskon 1 tidak boleh negatif.',
            'diskon1.max' => 'Diskon 1 maksimal 100%.',
            'diskon2.numeric' => 'Diskon 2 harus berupa angka.',
            'diskon2.min' => 'Diskon 2 tidak boleh negatif.',
            'diskon2.max' => 'Diskon 2 maksimal 100%.',
            'harga_nett.required' => 'Harga nett harus diisi.',
            'harga_nett.numeric' => 'Harga nett harus berupa angka.',
            'harga_nett.min' => 'Harga nett tidak boleh negatif.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Ensure divisi and penerimaan codes are available for validation
        $this->merge([
            'no_penerimaan' => $this->route('noPenerimaan'),
        ]);
    }
}
