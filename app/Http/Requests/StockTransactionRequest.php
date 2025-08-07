<?php

namespace App\Http\Requests;

use App\Models\DBarang;
use App\Models\KartuStok;
use Illuminate\Validation\Rule;

class StockTransactionRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            // Header validation
            'kodedivisi' => $this->divisionRules(),
            'noreferensi' => [
                'required',
                'string',
                'max:50',
                Rule::unique('dbo.kartu_stok', 'noreferensi')
                    ->where('kodedivisi', $this->kodedivisi)
                    ->ignore($this->route('stock_transaction'))
            ],
            'tanggal' => $this->dateRules(),
            'jenis' => [
                'required',
                'string',
                'in:IN,OUT,ADJUST,TRANSFER,RETURN'
            ],
            'tipetransaksi' => [
                'required',
                'string',
                'max:50'
            ],
            'keterangan' => 'nullable|string|max:255',
            'status' => [
                'required',
                'string',
                'in:Draft,Pending,Approved,Cancelled'
            ],

            // Items validation
            'items' => 'required|array|min:1|max:100',
            'items.*.kodebarang' => $this->itemCodeRules(),
            'items.*.qtymasuk' => [
                'nullable',
                'numeric',
                'min:0'
            ],
            'items.*.qtykeluar' => [
                'nullable',
                'numeric',
                'min:0'
            ],
            'items.*.harga' => $this->moneyRules(),
            'items.*.keterangan' => 'nullable|string|max:255',

            // Transfer specific validation
            'kodedivisi_tujuan' => [
                'required_if:jenis,TRANSFER',
                'string',
                'max:10',
                'exists:dbo.m_divisi,kodedivisi',
                'different:kodedivisi'
            ],
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $this->validateBusinessRules($validator);
        });
    }

    /**
     * Validate business rules
     */
    protected function validateBusinessRules($validator): void
    {
        if ($this->has('items')) {
            foreach ($this->items as $index => $item) {
                $this->validateItemTransaction($validator, $item, $index);
            }
        }
    }

    /**
     * Validate individual item transaction
     */
    protected function validateItemTransaction($validator, $item, $index): void
    {
        // Validate qty in vs qty out
        $qtyMasuk = $item['qtymasuk'] ?? 0;
        $qtyKeluar = $item['qtykeluar'] ?? 0;

        // Only one of qty masuk or keluar should be filled
        if ($qtyMasuk > 0 && $qtyKeluar > 0) {
            $validator->errors()->add(
                "items.{$index}.qtymasuk",
                'Cannot have both quantity in and quantity out for the same item'
            );
        }

        // At least one qty should be filled
        if ($qtyMasuk == 0 && $qtyKeluar == 0) {
            $validator->errors()->add(
                "items.{$index}.qtymasuk",
                'Either quantity in or quantity out must be greater than 0'
            );
        }

        // Validate based on transaction type
        switch ($this->jenis) {
            case 'IN':
                if ($qtyMasuk <= 0) {
                    $validator->errors()->add(
                        "items.{$index}.qtymasuk",
                        'Incoming transactions must have quantity in greater than 0'
                    );
                }
                break;

            case 'OUT':
            case 'TRANSFER':
                if ($qtyKeluar <= 0) {
                    $validator->errors()->add(
                        "items.{$index}.qtykeluar",
                        'Outgoing transactions must have quantity out greater than 0'
                    );
                }
                $this->validateStockAvailability($validator, $item, $index);
                break;

            case 'ADJUST':
                // For adjustments, allow both in and out but not both
                break;

            case 'RETURN':
                if ($qtyMasuk <= 0) {
                    $validator->errors()->add(
                        "items.{$index}.qtymasuk",
                        'Return transactions must have quantity in greater than 0'
                    );
                }
                break;
        }
    }

    /**
     * Validate stock availability for outgoing transactions
     */
    protected function validateStockAvailability($validator, $item, $index): void
    {
        $barang = DBarang::where('kodebarang', $item['kodebarang'])
                         ->where('kodedivisi', $this->kodedivisi)
                         ->first();

        if (!$barang) {
            $validator->errors()->add(
                "items.{$index}.kodebarang",
                'Item not found in this division'
            );
            return;
        }

        $currentStock = $barang->stok ?? 0;
        $qtyKeluar = $item['qtykeluar'] ?? 0;

        if ($qtyKeluar > $currentStock) {
            $validator->errors()->add(
                "items.{$index}.qtykeluar",
                "Insufficient stock. Available: {$currentStock}, Requested: {$qtyKeluar}"
            );
        }

        // Check for negative stock
        if (($currentStock - $qtyKeluar) < 0 && !$this->allowNegativeStock()) {
            $validator->errors()->add(
                "items.{$index}.qtykeluar",
                'Transaction would result in negative stock'
            );
        }
    }

    /**
     * Check if negative stock is allowed
     */
    protected function allowNegativeStock(): bool
    {
        return config('app.allow_negative_stock', false);
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'noreferensi.unique' => 'Reference number already exists in this division',
            'jenis.in' => 'Transaction type must be IN, OUT, ADJUST, TRANSFER, or RETURN',
            'status.in' => 'Status must be Draft, Pending, Approved, or Cancelled',
            'items.required' => 'Transaction must have at least one item',
            'items.min' => 'Transaction must have at least one item',
            'items.max' => 'Transaction cannot have more than 100 items',
            'items.*.kodebarang.exists' => 'The selected item does not exist',
            'kodedivisi_tujuan.required_if' => 'Destination division is required for transfer transactions',
            'kodedivisi_tujuan.different' => 'Destination division must be different from source division',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'kodedivisi' => strtoupper($this->kodedivisi ?? ''),
            'noreferensi' => strtoupper($this->noreferensi ?? ''),
            'jenis' => strtoupper($this->jenis ?? ''),
            'tipetransaksi' => strtoupper($this->tipetransaksi ?? ''),
            'status' => ucfirst(strtolower($this->status ?? '')),
            'kodedivisi_tujuan' => strtoupper($this->kodedivisi_tujuan ?? ''),
        ]);

        // Prepare items
        if ($this->has('items')) {
            $items = collect($this->items)->map(function ($item) {
                return array_merge($item, [
                    'kodebarang' => strtoupper($item['kodebarang'] ?? ''),
                    'qtymasuk' => floatval($item['qtymasuk'] ?? 0),
                    'qtykeluar' => floatval($item['qtykeluar'] ?? 0),
                    'harga' => floatval($item['harga'] ?? 0),
                ]);
            })->toArray();

            $this->merge(['items' => $items]);
        }
    }
}
