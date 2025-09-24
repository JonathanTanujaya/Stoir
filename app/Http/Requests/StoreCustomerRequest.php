<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCustomerRequest extends FormRequest
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
            'kode_cust' => [
                'required',
                'string',
                'max:5',
                Rule::unique('m_cust', 'kode_cust')
            ],
            'nama_cust' => 'required|string|max:100',
            'kode_area' => [
                'required',
                'string',
                'max:5',
                Rule::exists('m_area', 'kode_area')
            ],
            'alamat' => 'nullable|string|max:255',
            'telp' => 'nullable|string|max:20',
            'contact' => 'nullable|string|max:50',
            'credit_limit' => 'nullable|numeric|min:0|max:999999999999.99',
            'jatuh_tempo' => 'nullable|integer|min:0|max:365',
            'status' => 'nullable|boolean',
            'no_npwp' => 'nullable|string|max:20',
            'nama_pajak' => 'nullable|string|max:100',
            'alamat_pajak' => 'nullable|string|max:255',
            'kode_sales' => [
                'nullable',
                'string',
                'max:5',
                Rule::exists('m_sales', 'kode_sales')
            ]
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'kode_cust.required' => 'Kode customer wajib diisi',
            'kode_cust.unique' => 'Kode customer sudah ada dalam divisi ini',
            'kode_cust.max' => 'Kode customer maksimal 5 karakter',
            'nama_cust.required' => 'Nama customer wajib diisi',
            'nama_cust.max' => 'Nama customer maksimal 100 karakter',
            'kode_area.required' => 'Kode area wajib diisi',
            'kode_area.exists' => 'Kode area tidak valid atau tidak ada dalam divisi ini',
            'alamat.max' => 'Alamat maksimal 255 karakter',
            'telp.max' => 'Nomor telepon maksimal 20 karakter',
            'contact.max' => 'Nama kontak maksimal 50 karakter',
            'credit_limit.numeric' => 'Credit limit harus berupa angka',
            'credit_limit.min' => 'Credit limit tidak boleh negatif',
            'credit_limit.max' => 'Credit limit melebihi batas maksimum',
            'jatuh_tempo.integer' => 'Jatuh tempo harus berupa angka bulat',
            'jatuh_tempo.min' => 'Jatuh tempo tidak boleh negatif',
            'jatuh_tempo.max' => 'Jatuh tempo maksimal 365 hari',
            'status.boolean' => 'Status harus berupa true atau false',
            'no_npwp.max' => 'Nomor NPWP maksimal 20 karakter',
            'nama_pajak.max' => 'Nama pajak maksimal 100 karakter',
            'alamat_pajak.max' => 'Alamat pajak maksimal 255 karakter',
            'kode_sales.exists' => 'Kode sales tidak valid atau tidak ada dalam divisi ini'
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $kodeCust = $this->input('kode_cust');
        $kodeArea = $this->input('kode_area');
        $kodeSales = $this->input('kode_sales');

        $this->merge([
            'kode_cust' => is_string($kodeCust) ? strtoupper(trim($kodeCust)) : $kodeCust,
            'kode_area' => is_string($kodeArea) ? strtoupper(trim($kodeArea)) : $kodeArea,
            'kode_sales' => is_string($kodeSales) ? strtoupper(trim($kodeSales)) : $kodeSales,
            'status' => $this->boolean('status', true), // Default to active
            'credit_limit' => $this->input('credit_limit', 0), // Default credit limit
            'jatuh_tempo' => $this->input('jatuh_tempo', 30) // Default 30 days
        ]);
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'kode_cust' => 'kode customer',
            'nama_cust' => 'nama customer',
            'kode_area' => 'kode area',
            'alamat' => 'alamat',
            'telp' => 'nomor telepon',
            'contact' => 'nama kontak',
            'credit_limit' => 'credit limit',
            'jatuh_tempo' => 'jatuh tempo',
            'status' => 'status',
            'no_npwp' => 'nomor NPWP',
            'nama_pajak' => 'nama pajak',
            'alamat_pajak' => 'alamat pajak',
            'kode_sales' => 'kode sales'
        ];
    }
}
