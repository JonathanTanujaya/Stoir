<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreInvoiceRequest extends FormRequest
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
            'no_invoice' => [
                'required',
                'string',
                'max:15',
                'regex:/^[A-Z0-9\-_\/]+$/',
                Rule::unique('invoice', 'no_invoice')
            ],
            'tgl_faktur' => 'required|date',
            'kode_cust' => [
                'required',
                'string',
                'max:5',
                Rule::exists('m_cust', 'kode_cust')
            ],
            'kode_sales' => [
                'nullable',
                'string',
                'max:5',
                Rule::exists('m_sales', 'kode_sales')
            ],
            'tipe' => 'nullable|string|in:1,2',
            'jatuh_tempo' => 'required|date|after_or_equal:tgl_faktur',
            'total' => 'required|numeric|min:0|max:999999999.99',
            'disc' => 'nullable|numeric|min:0|max:999999999.99',
            'pajak' => 'nullable|numeric|min:0|max:999999999.99',
            'grand_total' => 'required|numeric|min:0|max:999999999.99',
            'sisa_invoice' => 'required|numeric|min:0|max:999999999.99',
            'ket' => 'nullable|string|max:255',
            'status' => 'required|string|in:Open,Lunas,Belum Lunas,Partial,Cancel',
            'username' => 'required|string|max:50',
            'tt' => 'nullable|string|max:15'
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'no_invoice.required' => 'No invoice wajib diisi',
            'no_invoice.string' => 'No invoice harus berupa teks',
            'no_invoice.max' => 'No invoice maksimal 15 karakter',
            'no_invoice.regex' => 'No invoice hanya boleh mengandung huruf kapital, angka, tanda minus, underscore, dan slash',
            'no_invoice.unique' => 'No invoice sudah digunakan dalam divisi ini',
            'tgl_faktur.required' => 'Tanggal faktur wajib diisi',
            'tgl_faktur.date' => 'Tanggal faktur harus berupa tanggal yang valid',
            'kode_cust.required' => 'Customer wajib dipilih',
            'kode_cust.exists' => 'Customer tidak ditemukan dalam divisi ini',
            'kode_sales.exists' => 'Sales tidak ditemukan dalam divisi ini',
            'tipe.in' => 'Tipe harus berupa 1 atau 2',
            'jatuh_tempo.required' => 'Tanggal jatuh tempo wajib diisi',
            'jatuh_tempo.date' => 'Tanggal jatuh tempo harus berupa tanggal yang valid',
            'jatuh_tempo.after_or_equal' => 'Tanggal jatuh tempo tidak boleh sebelum tanggal faktur',
            'total.required' => 'Total wajib diisi',
            'total.numeric' => 'Total harus berupa angka',
            'total.min' => 'Total tidak boleh kurang dari 0',
            'total.max' => 'Total tidak boleh lebih dari 999,999,999.99',
            'disc.numeric' => 'Diskon harus berupa angka',
            'disc.min' => 'Diskon tidak boleh kurang dari 0',
            'disc.max' => 'Diskon tidak boleh lebih dari 999,999,999.99',
            'pajak.numeric' => 'Pajak harus berupa angka',
            'pajak.min' => 'Pajak tidak boleh kurang dari 0',
            'pajak.max' => 'Pajak tidak boleh lebih dari 999,999,999.99',
            'grand_total.required' => 'Grand total wajib diisi',
            'grand_total.numeric' => 'Grand total harus berupa angka',
            'grand_total.min' => 'Grand total tidak boleh kurang dari 0',
            'grand_total.max' => 'Grand total tidak boleh lebih dari 999,999,999.99',
            'sisa_invoice.required' => 'Sisa invoice wajib diisi',
            'sisa_invoice.numeric' => 'Sisa invoice harus berupa angka',
            'sisa_invoice.min' => 'Sisa invoice tidak boleh kurang dari 0',
            'sisa_invoice.max' => 'Sisa invoice tidak boleh lebih dari 999,999,999.99',
            'ket.string' => 'Keterangan harus berupa teks',
            'ket.max' => 'Keterangan maksimal 255 karakter',
            'status.required' => 'Status wajib dipilih',
            'status.in' => 'Status harus berupa Open, Lunas, Belum Lunas, Partial, atau Cancel',
            'username.required' => 'Username wajib diisi',
            'username.string' => 'Username harus berupa teks',
            'username.max' => 'Username maksimal 50 karakter',
            'tt.string' => 'TT harus berupa teks',
            'tt.max' => 'TT maksimal 15 karakter'
        ];
    }

    /**
     * Get custom attribute names for validation errors.
     */
    public function attributes(): array
    {
        return [
            'no_invoice' => 'no invoice',
            'tgl_faktur' => 'tanggal faktur',
            'kode_cust' => 'customer',
            'kode_sales' => 'sales',
            'tipe' => 'tipe',
            'jatuh_tempo' => 'tanggal jatuh tempo',
            'total' => 'total',
            'disc' => 'diskon',
            'pajak' => 'pajak',
            'grand_total' => 'grand total',
            'sisa_invoice' => 'sisa invoice',
            'ket' => 'keterangan',
            'status' => 'status',
            'username' => 'username',
            'tt' => 'TT'
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'no_invoice' => strtoupper($this->no_invoice ?? ''),
            'kode_cust' => strtoupper($this->kode_cust ?? ''),
            'kode_sales' => strtoupper($this->kode_sales ?? ''),
            'status' => $this->status ?? 'Open'
        ]);
    }
}
