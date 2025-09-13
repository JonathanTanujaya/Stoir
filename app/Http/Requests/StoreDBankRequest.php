<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDBankRequest extends FormRequest
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
            'kode_bank' => $this->route('kodeBank'),
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
            'kode_divisi' => 'required|string|max:5',
            'no_rekening' => [
                'required',
                'string',
                'max:50',
                Rule::unique('d_bank')->where(function ($query) {
                    return $query->where('kode_divisi', $this->route('kodeDivisi'));
                })
            ],
            'kode_bank' => 'nullable|string|max:5',
            'atas_nama' => 'required|string|max:50',
            'saldo' => 'nullable|numeric|min:0|max:99999999999999.99',
            'status' => 'nullable|string|max:50',
        ];
    }

    /**
     * Get custom validation error messages.
     */
    public function messages(): array
    {
        return [
            'kode_divisi.required' => 'Kode divisi wajib diisi.',
            'kode_divisi.string' => 'Kode divisi harus berupa teks.',
            'kode_divisi.max' => 'Kode divisi maksimal 5 karakter.',
            'no_rekening.required' => 'Nomor rekening wajib diisi.',
            'no_rekening.string' => 'Nomor rekening harus berupa teks.',
            'no_rekening.max' => 'Nomor rekening maksimal 50 karakter.',
            'no_rekening.unique' => 'Nomor rekening sudah ada dalam divisi ini.',
            'kode_bank.string' => 'Kode bank harus berupa teks.',
            'kode_bank.max' => 'Kode bank maksimal 5 karakter.',
            'atas_nama.required' => 'Atas nama wajib diisi.',
            'atas_nama.string' => 'Atas nama harus berupa teks.',
            'atas_nama.max' => 'Atas nama maksimal 50 karakter.',
            'saldo.numeric' => 'Saldo harus berupa angka.',
            'saldo.min' => 'Saldo tidak boleh negatif.',
            'saldo.max' => 'Saldo melebihi batas maksimal.',
            'status.string' => 'Status harus berupa teks.',
            'status.max' => 'Status maksimal 50 karakter.',
        ];
    }
}
