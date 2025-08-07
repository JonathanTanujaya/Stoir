<?php

namespace App\Http\Requests;

use App\Models\MCust;
use App\Models\MSales;
use App\Models\DBarang;
use Illuminate\Validation\Rule;

class InvoiceRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            // Header validation
            'kodedivisi' => $this->divisionRules(),
            'noinvoice' => [
                'required',
                'string',
                'max:50',
                Rule::unique('dbo.invoice', 'noinvoice')
                    ->where('kodedivisi', $this->kodedivisi)
                    ->ignore($this->route('invoice'))
            ],
            'tanggal' => $this->dateRules(),
            'kodecust' => $this->customerCodeRules(),
            'kodesales' => [
                'required',
                'string',
                'max:20',
                'exists:dbo.m_sales,kodesales'
            ],
            'tipe' => [
                'required',
                'string',
                'in:CASH,CREDIT,TRANSFER'
            ],
            'total' => $this->moneyRules(),
            'diskon' => [
                'nullable',
                'numeric',
                'min:0',
                'max:100' // percentage
            ],
            'pajak' => [
                'nullable',
                'numeric',
                'min:0',
                'max:100' // percentage
            ],
            'keterangan' => 'nullable|string|max:255',
            'status' => [
                'required',
                'string',
                'in:Draft,Pending,Approved,Cancelled'
            ],

            // Detail validation
            'details' => 'required|array|min:1|max:100',
            'details.*.kodebarang' => $this->itemCodeRules(),
            'details.*.qty' => $this->quantityRules(),
            'details.*.harga' => $this->moneyRules(),
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
            'details.*.harganett' => $this->moneyRules(),
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
        // Check customer credit limit for CREDIT invoices
        if ($this->tipe === 'CREDIT') {
            $customer = MCust::where('kodecust', $this->kodecust)->first();
            if ($customer && $customer->credit_limit > 0) {
                $currentDebt = $customer->getCurrentDebt();
                if (($currentDebt + $this->total) > $customer->credit_limit) {
                    $validator->errors()->add('total', 'Invoice total exceeds customer credit limit');
                }
            }
        }

        // Validate stock availability
        if ($this->has('details')) {
            foreach ($this->details as $index => $detail) {
                $item = DBarang::where('kodebarang', $detail['kodebarang'])->first();
                if ($item && $item->stok < $detail['qty']) {
                    $validator->errors()->add(
                        "details.{$index}.qty", 
                        "Insufficient stock. Available: {$item->stok}, Requested: {$detail['qty']}"
                    );
                }
            }
        }

        // Validate calculated totals
        if ($this->has('details')) {
            $calculatedTotal = 0;
            foreach ($this->details as $detail) {
                $lineTotal = $detail['qty'] * $detail['harganett'];
                $calculatedTotal += $lineTotal;
            }

            // Apply discounts and taxes
            if ($this->diskon) {
                $calculatedTotal -= ($calculatedTotal * $this->diskon / 100);
            }
            if ($this->pajak) {
                $calculatedTotal += ($calculatedTotal * $this->pajak / 100);
            }

            $difference = abs($calculatedTotal - $this->total);
            if ($difference > 0.01) { // Allow 1 cent tolerance
                $validator->errors()->add('total', 'Total amount does not match calculated total');
            }
        }
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'noinvoice.unique' => 'Invoice number already exists in this division',
            'kodecust.exists' => 'The selected customer does not exist',
            'kodesales.exists' => 'The selected sales person does not exist',
            'tipe.in' => 'Payment type must be CASH, CREDIT, or TRANSFER',
            'status.in' => 'Status must be Draft, Pending, Approved, or Cancelled',
            'details.required' => 'Invoice must have at least one item',
            'details.min' => 'Invoice must have at least one item',
            'details.max' => 'Invoice cannot have more than 100 items',
            'details.*.kodebarang.exists' => 'The selected item does not exist',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'kodedivisi' => strtoupper($this->kodedivisi ?? ''),
            'noinvoice' => strtoupper($this->noinvoice ?? ''),
            'kodecust' => strtoupper($this->kodecust ?? ''),
            'kodesales' => strtoupper($this->kodesales ?? ''),
            'tipe' => strtoupper($this->tipe ?? ''),
            'status' => ucfirst(strtolower($this->status ?? '')),
        ]);

        // Prepare details
        if ($this->has('details')) {
            $details = collect($this->details)->map(function ($detail) {
                return array_merge($detail, [
                    'kodebarang' => strtoupper($detail['kodebarang'] ?? ''),
                ]);
            })->toArray();

            $this->merge(['details' => $details]);
        }
    }
}
