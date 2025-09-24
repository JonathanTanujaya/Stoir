<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateInvoiceRequest extends FormRequest
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
            'tgl_faktur' => 'sometimes|required|date',
            'kode_cust' => [
                'sometimes',
                'required',
                'string',
                'max:5',
                Rule::exists('m_cust', 'kode_cust')
            ],
            'kode_sales' => [
                'sometimes',
                'nullable',
                'string',
                'max:5',
                Rule::exists('m_sales', 'kode_sales')
            ],
            'tipe' => 'sometimes|nullable|string|in:1,2',
            'jatuh_tempo' => 'sometimes|required|date|after_or_equal:tgl_faktur',
            'total' => 'sometimes|required|numeric|min:0|max:999999999.99',
            'disc' => 'sometimes|nullable|numeric|min:0|max:999999999.99',
            'pajak' => 'sometimes|nullable|numeric|min:0|max:999999999.99',
            'grand_total' => 'sometimes|required|numeric|min:0|max:999999999.99',
            'sisa_invoice' => 'sometimes|required|numeric|min:0|max:999999999.99',
            'ket' => 'sometimes|nullable|string|max:255',
            'status' => 'sometimes|required|string|in:Open,Lunas,Belum Lunas,Partial,Cancel',
            'username' => 'sometimes|required|string|max:50',
            'tt' => 'sometimes|nullable|string|max:15'
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
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
        if ($this->has('kode_cust')) {
            $this->merge([
                'kode_cust' => strtoupper($this->kode_cust)
            ]);
        }
        
        if ($this->has('kode_sales')) {
            $this->merge([
                'kode_sales' => strtoupper($this->kode_sales)
            ]);
        }

        if ($this->has('status')) {
            $this->merge([
                'status' => $this->status ?? 'Open'
            ]);
        }
    }
}
