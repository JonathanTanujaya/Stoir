<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCustomerRequest extends FormRequest
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
        $kodeCust = $this->route('kodeCust');
        
        return [
            'kode_cust' => [
                'sometimes',
                'string',
                'max:5',
                Rule::unique('m_cust', 'kode_cust')
                    ->ignore($kodeCust, 'kode_cust')
            ],
            'nama_cust' => 'sometimes|string|max:100',
            'kode_area' => [
                'sometimes',
                'string',
                'max:5',
                Rule::exists('m_area', 'kode_area')
            ],
            'alamat' => 'sometimes|nullable|string|max:255',
            'telp' => 'sometimes|nullable|string|max:20',
            'contact' => 'sometimes|nullable|string|max:50',
            'credit_limit' => 'sometimes|nullable|numeric|min:0|max:999999999999.99',
            'jatuh_tempo' => 'sometimes|nullable|integer|min:0|max:365',
            'status' => 'sometimes|nullable|boolean',
            'no_npwp' => 'sometimes|nullable|string|max:20',
            'nama_pajak' => 'sometimes|nullable|string|max:100',
            'alamat_pajak' => 'sometimes|nullable|string|max:255',
            'kode_sales' => [
                'sometimes',
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
            'kode_cust.unique' => 'Kode customer sudah ada dalam divisi ini',
            'kode_cust.max' => 'Kode customer maksimal 15 karakter',
            'nama_cust.max' => 'Nama customer maksimal 100 karakter',
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

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $payload = [];
        foreach (['kode_cust', 'kode_area', 'kode_sales'] as $key) {
            if ($this->has($key)) {
                $value = $this->input($key);
                $payload[$key] = is_string($value) ? strtoupper(trim($value)) : $value;
            }
        }
        if ($this->has('status')) {
            $payload['status'] = $this->boolean('status');
        }
        if (! empty($payload)) {
            $this->merge($payload);
        }
    }
}
