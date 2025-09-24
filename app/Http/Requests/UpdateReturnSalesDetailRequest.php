<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReturnSalesDetailRequest extends FormRequest
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
            'no_invoice' => [
                'sometimes',
                'string',
                'max:15',
                'exists:invoice,no_invoice',
            ],
            'kode_barang' => [
                'sometimes',
                'string',
                'max:30',
                'exists:m_barang,kode_barang',
            ],
            'qty_retur' => [
                'sometimes',
                'integer',
                'min:1',
            ],
            'harga_nett' => [
                'sometimes',
                'numeric',
                'min:0',
            ],
            'status' => [
                'sometimes',
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
            'no_invoice.exists' => 'Nomor invoice tidak ditemukan dalam divisi ini.',
            'kode_barang.exists' => 'Kode barang tidak ditemukan dalam divisi ini.',
            'qty_retur.integer' => 'Quantity retur harus berupa angka.',
            'qty_retur.min' => 'Quantity retur minimal 1.',
            'harga_nett.numeric' => 'Harga nett harus berupa angka.',
            'harga_nett.min' => 'Harga nett tidak boleh negatif.',
            'status.in' => 'Status harus berupa Open, Finish, atau Cancel.',
        ];
    }
}
