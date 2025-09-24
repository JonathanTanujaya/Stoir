<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSalesRequest extends FormRequest
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
            'nama_sales' => 'sometimes|required|string|max:50',
            'kode_area' => [
                'sometimes',
                'nullable',
                'string',
                'max:5',
                Rule::exists('m_area', 'kode_area')
            ],
            'alamat' => 'sometimes|nullable|string|max:500',
            'no_hp' => 'sometimes|nullable|string|max:20|regex:/^[\d\-\+\(\)\s]+$/',
            'target' => 'sometimes|nullable|numeric|min:0|max:999999999.99',
            'status' => 'sometimes|nullable|boolean'
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'nama_sales.required' => 'Nama sales wajib diisi',
            'nama_sales.string' => 'Nama sales harus berupa teks',
            'nama_sales.max' => 'Nama sales maksimal 50 karakter',
            'kode_area.string' => 'Kode area harus berupa teks',
            'kode_area.max' => 'Kode area maksimal 5 karakter',
            'kode_area.exists' => 'Area tidak ditemukan dalam divisi ini',
            'alamat.string' => 'Alamat harus berupa teks',
            'alamat.max' => 'Alamat maksimal 500 karakter',
            'no_hp.string' => 'No HP harus berupa teks',
            'no_hp.max' => 'No HP maksimal 20 karakter',
            'no_hp.regex' => 'Format No HP tidak valid. Gunakan angka, tanda minus, plus, kurung, dan spasi',
            'target.numeric' => 'Target harus berupa angka',
            'target.min' => 'Target tidak boleh kurang dari 0',
            'target.max' => 'Target tidak boleh lebih dari 999,999,999.99',
            'status.boolean' => 'Status harus berupa true atau false'
        ];
    }

    /**
     * Get custom attribute names for validation errors.
     */
    public function attributes(): array
    {
        return [
            'nama_sales' => 'nama sales',
            'kode_area' => 'kode area',
            'alamat' => 'alamat',
            'no_hp' => 'no HP',
            'target' => 'target',
            'status' => 'status'
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $payload = [];
        if ($this->has('kode_area')) {
            $payload['kode_area'] = strtoupper((string) $this->input('kode_area'));
        }
        if ($this->has('status')) {
            $payload['status'] = filter_var($this->input('status'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? $this->input('status');
        }
        if (! empty($payload)) {
            $this->merge($payload);
        }
    }
}
