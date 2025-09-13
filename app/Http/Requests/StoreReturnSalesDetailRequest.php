<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReturnSalesDetailRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'kode_divisi' => $this->route('kodeDivisi'),
            'no_retur' => $this->route('noRetur'),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'kode_divisi' => [
                'required',
                'string',
                'max:5',
                'exists:m_divisi,kode_divisi',
            ],
            'no_retur' => [
                'required',
                'string',
                'max:15',
                'exists:return_sales,no_return_sales,kode_divisi,' . $this->route('kodeDivisi'),
            ],
            'no_invoice' => [
                'required',
                'string',
                'max:15',
                'exists:invoice,no_invoice,kode_divisi,' . $this->route('kodeDivisi'),
            ],
            'kode_barang' => [
                'required',
                'string',
                'max:30',
                'exists:m_barang,kode_barang,kode_divisi,' . $this->route('kodeDivisi'),
            ],
            'qty_retur' => [
                'required',
                'integer',
                'min:1',
            ],
            'harga_nett' => [
                'required',
                'numeric',
                'min:0',
            ],
            'status' => [
                'nullable',
                'string',
                'max:20',
                'in:Open,Finish,Cancel',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'kode_divisi.required' => 'Kode divisi harus diisi.',
            'kode_divisi.exists' => 'Kode divisi tidak valid.',
            'no_retur.required' => 'Nomor retur harus diisi.',
            'no_retur.exists' => 'Nomor retur tidak ditemukan dalam divisi ini.',
            'no_invoice.required' => 'Nomor invoice harus diisi.',
            'no_invoice.exists' => 'Nomor invoice tidak ditemukan dalam divisi ini.',
            'kode_barang.required' => 'Kode barang harus diisi.',
            'kode_barang.exists' => 'Kode barang tidak ditemukan dalam divisi ini.',
            'qty_retur.required' => 'Quantity retur harus diisi.',
            'qty_retur.integer' => 'Quantity retur harus berupa angka.',
            'qty_retur.min' => 'Quantity retur minimal 1.',
            'harga_nett.required' => 'Harga nett harus diisi.',
            'harga_nett.numeric' => 'Harga nett harus berupa angka.',
            'harga_nett.min' => 'Harga nett tidak boleh negatif.',
            'status.in' => 'Status harus berupa Open, Finish, atau Cancel.',
        ];
    }
}
