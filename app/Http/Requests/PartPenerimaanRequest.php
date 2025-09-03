<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class PartPenerimaanRequest extends FormRequest
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
            'kode_divisi' => 'required|string|max:10|exists:m_divisi,kode_divisi',
            'no_penerimaan' => 'required|string|max:20|unique:part_penerimaan,no_penerimaan,NULL,id,kode_divisi,' . $this->kode_divisi,
            'tgl_penerimaan' => 'required|date|after_or_equal:today',
            'kode_supplier' => 'required|string|max:10|exists:m_supplier,kode_supplier,kode_divisi,' . $this->kode_divisi,
            'jatuh_tempo' => 'required|date|after_or_equal:tgl_penerimaan',
            'no_faktur' => 'required|string|max:20',
            'kode_valas' => 'nullable|string|max:3',
            'kurs' => 'nullable|numeric|min:0.0001',
            'pajak_persen' => 'nullable|numeric|min:0|max:100',
            
            // Details validation
            'details' => 'required|array|min:1|max:50',
            'details.*.kode_barang' => 'required|string|max:20|exists:m_barang,kode_barang,kode_divisi,' . $this->kode_divisi,
            'details.*.qty_supply' => 'required|integer|min:1|max:999999',
            'details.*.harga' => 'required|numeric|min:0|max:999999999.99',
            'details.*.diskon1' => 'nullable|numeric|min:0|max:100',
            'details.*.diskon2' => 'nullable|numeric|min:0|max:100'
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'kode_divisi.required' => 'Kode divisi harus diisi',
            'kode_divisi.exists' => 'Kode divisi tidak valid',
            'no_penerimaan.required' => 'Nomor penerimaan harus diisi',
            'no_penerimaan.unique' => 'Nomor penerimaan sudah digunakan untuk divisi ini',
            'tgl_penerimaan.required' => 'Tanggal penerimaan harus diisi',
            'tgl_penerimaan.after_or_equal' => 'Tanggal penerimaan tidak boleh kurang dari hari ini',
            'kode_supplier.required' => 'Supplier harus dipilih',
            'kode_supplier.exists' => 'Supplier tidak valid untuk divisi ini',
            'jatuh_tempo.required' => 'Tanggal jatuh tempo harus diisi',
            'jatuh_tempo.after_or_equal' => 'Tanggal jatuh tempo tidak boleh kurang dari tanggal penerimaan',
            'no_faktur.required' => 'Nomor faktur supplier harus diisi',
            'kurs.min' => 'Nilai kurs harus lebih dari 0',
            'pajak_persen.max' => 'Persentase pajak tidak boleh lebih dari 100%',
            
            'details.required' => 'Detail barang harus diisi',
            'details.min' => 'Minimal harus ada 1 item barang',
            'details.max' => 'Maksimal 50 item barang per transaksi',
            'details.*.kode_barang.required' => 'Kode barang harus diisi',
            'details.*.kode_barang.exists' => 'Kode barang tidak valid untuk divisi ini',
            'details.*.qty_supply.required' => 'Qty supply harus diisi',
            'details.*.qty_supply.min' => 'Qty supply minimal 1',
            'details.*.qty_supply.max' => 'Qty supply maksimal 999,999',
            'details.*.harga.required' => 'Harga harus diisi',
            'details.*.harga.min' => 'Harga tidak boleh negatif',
            'details.*.harga.max' => 'Harga terlalu besar',
            'details.*.diskon1.max' => 'Diskon 1 tidak boleh lebih dari 100%',
            'details.*.diskon2.max' => 'Diskon 2 tidak boleh lebih dari 100%'
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            // Validasi business rules tambahan
            $this->validateBusinessRules($validator);
        });
    }

    /**
     * Custom business rules validation
     */
    private function validateBusinessRules(Validator $validator): void
    {
        // Check if kurs is required when valas is not IDR
        if ($this->kode_valas && $this->kode_valas !== 'IDR' && !$this->kurs) {
            $validator->errors()->add('kurs', 'Kurs harus diisi untuk mata uang selain IDR');
        }

        // Validate duplicate items
        if ($this->details) {
            $kodeBarangList = collect($this->details)->pluck('kode_barang');
            $duplicates = $kodeBarangList->duplicates();
            
            if ($duplicates->isNotEmpty()) {
                $validator->errors()->add('details', 'Terdapat kode barang yang duplikat: ' . $duplicates->join(', '));
            }
        }

        // Validate total calculations
        if ($this->details) {
            $totalBruto = 0;
            foreach ($this->details as $index => $detail) {
                $subtotal = ($detail['qty_supply'] ?? 0) * ($detail['harga'] ?? 0);
                $totalBruto += $subtotal;

                // Validate individual item total
                if ($subtotal > 999999999.99) {
                    $validator->errors()->add("details.{$index}", 'Total item terlalu besar (Qty × Harga)');
                }
            }

            // Validate grand total
            if ($totalBruto > 9999999999.99) {
                $validator->errors()->add('details', 'Total penerimaan terlalu besar');
            }
        }
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422)
        );
    }

    /**
     * Get the validated data from the request with proper formatting.
     */
    public function validated($key = null, $default = null): array
    {
        $validated = parent::validated($key, $default);
        
        // Format data jika diperlukan
        if (isset($validated['kurs']) && !$validated['kurs']) {
            $validated['kurs'] = 1;
        }
        
        if (isset($validated['kode_valas']) && !$validated['kode_valas']) {
            $validated['kode_valas'] = 'IDR';
        }

        // Ensure diskon fields have default values
        if (isset($validated['details'])) {
            foreach ($validated['details'] as &$detail) {
                $detail['diskon1'] = $detail['diskon1'] ?? 0;
                $detail['diskon2'] = $detail['diskon2'] ?? 0;
            }
        }

        return $validated;
    }
}
