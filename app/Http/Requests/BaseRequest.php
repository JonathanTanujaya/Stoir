<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class BaseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422)
        );
    }

    /**
     * Get common validation rules for division codes
     */
    protected function divisionRules(): array
    {
        return [
            'required',
            'string',
            'max:10',
            'regex:/^[A-Z0-9]+$/',
            'exists:dbo.m_divisi,kodedivisi'
        ];
    }

    /**
     * Get common validation rules for currency/money fields
     */
    protected function moneyRules(): array
    {
        return [
            'required',
            'numeric',
            'min:0',
            'max:999999999.9999'
        ];
    }

    /**
     * Get common validation rules for quantities
     */
    protected function quantityRules(): array
    {
        return [
            'required',
            'numeric',
            'min:0',
            'max:99999.9999'
        ];
    }

    /**
     * Get common validation rules for dates
     */
    protected function dateRules(): array
    {
        return [
            'required',
            'date',
            'after_or_equal:2020-01-01',
            'before_or_equal:' . date('Y-m-d', strtotime('+1 year'))
        ];
    }

    /**
     * Get common validation rules for item codes
     */
    protected function itemCodeRules(): array
    {
        return [
            'required',
            'string',
            'max:50',
            'exists:dbo.d_barang,kodebarang'
        ];
    }

    /**
     * Get common validation rules for customer codes
     */
    protected function customerCodeRules(): array
    {
        return [
            'required',
            'string',
            'max:20',
            'exists:dbo.m_cust,kodecust'
        ];
    }

    /**
     * Get common validation rules for supplier codes
     */
    protected function supplierCodeRules(): array
    {
        return [
            'required',
            'string',
            'max:20',
            'exists:dbo.m_supplier,kodesupplier'
        ];
    }
}
