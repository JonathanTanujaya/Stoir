<?php
// File: app/Http/Requests/InvoiceRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InvoiceRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            // Header validation dengan field name baru
            'kode_divisi' => [
                'required',
                'string',
                'max:5',
                'exists:M_DIVISI,KODE_DIVISI'
            ],
            'no_invoice' => [
                'nullable',
                'string',
                'max:15',
                Rule::unique('INVOICE', 'NO_INVOICE')
                    ->where('KODE_DIVISI', $this->kode_divisi)
            ],
            'tgl_invoice' => [
                'required',
                'date'
            ],
            'kode_cust' => [
                'required',
                'string',
                'max:5',
                'exists:M_CUST,KODE_CUST'
            ],
            'kode_sales' => [
                'required',
                'string',
                'max:5',
                'exists:M_SALES,KODE_SALES'
            ],
            'tipe' => [
                'required',
                'string',
                'in:CASH,CREDIT,TRANSFER'
            ],
            'jatuh_tempo' => [
                'required',
                'date',
                'after_or_equal:tgl_invoice'
            ],
            'total' => [
                'required',
                'numeric',
                'min:0'
            ],
            'disc' => [
                'nullable',
                'numeric',
                'min:0',
                'max:100'
            ],
            'pajak' => [
                'nullable',
                'numeric',
                'min:0',
                'max:100'
            ],
            'grand_total' => [
                'required',
                'numeric',
                'min:0'
            ],
            'ket' => [
                'nullable',
                'string',
                'max:500'
            ],
            
            // Detail validation dengan field name baru
            'details' => [
                'required',
                'array',
                'min:1'
            ],
            'details.*.kode_barang' => [
                'required',
                'string',
                'max:30',
                'exists:M_BARANG,KODE_BARANG'
            ],
            'details.*.qty_supply' => [
                'required',
                'integer',
                'min:1'
            ],
            'details.*.harga_jual' => [
                'required',
                'numeric',
                'min:0'
            ],
            'details.*.diskon1' => [
                'nullable',
                'numeric',
                'min:0',
                'max:100'
            ],
            'details.*.diskon2' => [
                'nullable',
                'numeric',
                'min:0',
                'max:100'
            ],
            'details.*.harga_nett' => [
                'required',
                'numeric',
                'min:0'
            ],
            'details.*.jenis' => [
                'nullable',
                'string',
                'in:A,B,C'
            ]
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'kode_divisi.required' => 'Kode divisi wajib diisi',
            'kode_divisi.exists' => 'Kode divisi tidak valid',
            'no_invoice.unique' => 'Nomor invoice sudah digunakan',
            'tgl_invoice.required' => 'Tanggal invoice wajib diisi',
            'kode_cust.required' => 'Customer wajib dipilih',
            'kode_cust.exists' => 'Customer tidak valid',
            'kode_sales.required' => 'Sales wajib dipilih',
            'kode_sales.exists' => 'Sales tidak valid',
            'tipe.required' => 'Tipe invoice wajib dipilih',
            'tipe.in' => 'Tipe invoice harus CASH, CREDIT, atau TRANSFER',
            'jatuh_tempo.after_or_equal' => 'Jatuh tempo tidak boleh sebelum tanggal invoice',
            'total.required' => 'Total wajib diisi',
            'total.min' => 'Total tidak boleh negatif',
            'grand_total.required' => 'Grand total wajib diisi',
            'grand_total.min' => 'Grand total tidak boleh negatif',
            'details.required' => 'Detail barang wajib diisi',
            'details.min' => 'Minimal 1 barang harus diisi',
            'details.*.kode_barang.required' => 'Kode barang wajib diisi',
            'details.*.kode_barang.exists' => 'Kode barang tidak valid',
            'details.*.qty_supply.required' => 'Quantity wajib diisi',
            'details.*.qty_supply.min' => 'Quantity minimal 1',
            'details.*.harga_jual.required' => 'Harga jual wajib diisi',
            'details.*.harga_jual.min' => 'Harga jual tidak boleh negatif',
            'details.*.harga_nett.required' => 'Harga nett wajib diisi',
            'details.*.harga_nett.min' => 'Harga nett tidak boleh negatif'
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'kode_divisi' => 'Kode Divisi',
            'no_invoice' => 'Nomor Invoice',
            'tgl_invoice' => 'Tanggal Invoice',
            'kode_cust' => 'Customer',
            'kode_sales' => 'Sales',
            'tipe' => 'Tipe Invoice',
            'jatuh_tempo' => 'Jatuh Tempo',
            'total' => 'Total',
            'disc' => 'Diskon',
            'pajak' => 'Pajak',
            'grand_total' => 'Grand Total',
            'ket' => 'Keterangan',
            'details' => 'Detail Barang',
            'details.*.kode_barang' => 'Kode Barang',
            'details.*.qty_supply' => 'Quantity',
            'details.*.harga_jual' => 'Harga Jual',
            'details.*.diskon1' => 'Diskon 1',
            'details.*.diskon2' => 'Diskon 2',
            'details.*.harga_nett' => 'Harga Nett',
            'details.*.jenis' => 'Jenis'
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Auto-calculate grand total if not provided
        if (!$this->has('grand_total') && $this->has('total')) {
            $total = $this->input('total', 0);
            $disc = $this->input('disc', 0);
            $pajak = $this->input('pajak', 0);
            
            $afterDiscount = $total - ($total * $disc / 100);
            $grandTotal = $afterDiscount + ($afterDiscount * $pajak / 100);
            
            $this->merge([
                'grand_total' => $grandTotal,
                'sisa_invoice' => $grandTotal
            ]);
        }
        
        // Set default status
        if (!$this->has('status')) {
            $this->merge(['status' => 'Active']);
        }
        
        // Set default lunas
        if (!$this->has('lunas')) {
            $this->merge(['lunas' => false]);
        }
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Custom validation: Check stock availability
            if ($this->has('details')) {
                foreach ($this->input('details') as $index => $detail) {
                    // Check stock availability (akan ditangani oleh stored procedure)
                    // Validasi ini bisa ditambahkan jika diperlukan
                }
            }
            
            // Custom validation: Validate grand total calculation
            if ($this->has('total') && $this->has('grand_total')) {
                $total = $this->input('total');
                $disc = $this->input('disc', 0);
                $pajak = $this->input('pajak', 0);
                
                $expectedGrandTotal = $total - ($total * $disc / 100);
                $expectedGrandTotal += ($expectedGrandTotal * $pajak / 100);
                
                if (abs($this->input('grand_total') - $expectedGrandTotal) > 0.01) {
                    $validator->errors()->add('grand_total', 'Grand total calculation is incorrect');
                }
            }
        });
    }
}