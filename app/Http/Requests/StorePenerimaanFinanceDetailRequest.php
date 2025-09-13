<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePenerimaanFinanceDetailRequest extends FormRequest
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
            'no_penerimaan' => $this->route('noPenerimaan'),
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
            'no_penerimaan' => 'required|string|max:15',
            'no_invoice' => [
                'required',
                'string',
                'max:15',
                'exists:invoice,no_invoice,kode_divisi,' . $this->route('kodeDivisi')
            ],
            'jumlah_invoice' => 'required|numeric|min:0|max:99999999999999.99',
            'sisa_invoice' => 'required|numeric|min:0|max:99999999999999.99',
            'jumlah_bayar' => 'required|numeric|min:0|max:99999999999999.99',
            'jumlah_dispensasi' => 'required|numeric|min:0|max:99999999999999.99',
            'status' => 'nullable|string|max:20|in:Open,Finish,Cancel',
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
            'no_penerimaan.required' => 'Nomor penerimaan wajib diisi.',
            'no_penerimaan.string' => 'Nomor penerimaan harus berupa teks.',
            'no_penerimaan.max' => 'Nomor penerimaan maksimal 15 karakter.',
            'no_invoice.required' => 'Nomor invoice wajib diisi.',
            'no_invoice.string' => 'Nomor invoice harus berupa teks.',
            'no_invoice.max' => 'Nomor invoice maksimal 15 karakter.',
            'no_invoice.exists' => 'Invoice tidak ditemukan atau tidak terkait dengan divisi ini.',
            'jumlah_invoice.required' => 'Jumlah invoice wajib diisi.',
            'jumlah_invoice.numeric' => 'Jumlah invoice harus berupa angka.',
            'jumlah_invoice.min' => 'Jumlah invoice tidak boleh negatif.',
            'jumlah_invoice.max' => 'Jumlah invoice melebihi batas maksimal.',
            'sisa_invoice.required' => 'Sisa invoice wajib diisi.',
            'sisa_invoice.numeric' => 'Sisa invoice harus berupa angka.',
            'sisa_invoice.min' => 'Sisa invoice tidak boleh negatif.',
            'sisa_invoice.max' => 'Sisa invoice melebihi batas maksimal.',
            'jumlah_bayar.required' => 'Jumlah bayar wajib diisi.',
            'jumlah_bayar.numeric' => 'Jumlah bayar harus berupa angka.',
            'jumlah_bayar.min' => 'Jumlah bayar tidak boleh negatif.',
            'jumlah_bayar.max' => 'Jumlah bayar melebihi batas maksimal.',
            'jumlah_dispensasi.required' => 'Jumlah dispensasi wajib diisi.',
            'jumlah_dispensasi.numeric' => 'Jumlah dispensasi harus berupa angka.',
            'jumlah_dispensasi.min' => 'Jumlah dispensasi tidak boleh negatif.',
            'jumlah_dispensasi.max' => 'Jumlah dispensasi melebihi batas maksimal.',
            'status.string' => 'Status harus berupa teks.',
            'status.max' => 'Status maksimal 20 karakter.',
            'status.in' => 'Status harus salah satu dari: Open, Finish, Cancel.',
        ];
    }
}
