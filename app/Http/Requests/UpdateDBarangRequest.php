<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDBarangRequest extends FormRequest
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
            'kode_barang' => $this->route('kodeBarang'),
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
            'kode_divisi' => 'sometimes|required|string|max:5',
            'kode_barang' => 'sometimes|required|string|max:30',
            'tgl_masuk' => 'nullable|date',
            'modal' => 'nullable|numeric|min:0|max:99999999999999.99',
            'stok' => 'nullable|integer|min:0',
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
            'kode_barang.required' => 'Kode barang wajib diisi.',
            'kode_barang.string' => 'Kode barang harus berupa teks.',
            'kode_barang.max' => 'Kode barang maksimal 30 karakter.',
            'tgl_masuk.date' => 'Tanggal masuk harus berupa tanggal yang valid.',
            'modal.numeric' => 'Modal harus berupa angka.',
            'modal.min' => 'Modal tidak boleh negatif.',
            'modal.max' => 'Modal melebihi batas maksimal.',
            'stok.integer' => 'Stok harus berupa bilangan bulat.',
            'stok.min' => 'Stok tidak boleh negatif.',
        ];
    }
}
