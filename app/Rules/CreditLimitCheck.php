<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\MCust;
use App\Models\Invoice;

class CreditLimitCheck implements ValidationRule
{
    protected $kodecust;
    protected $excludeInvoice;

    public function __construct($kodecust, $excludeInvoice = null)
    {
        $this->kodecust = $kodecust;
        $this->excludeInvoice = $excludeInvoice;
    }

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $customer = MCust::where('kodecust', $this->kodecust)->first();

        if (!$customer) {
            $fail('Customer not found.');
            return;
        }

        if ($customer->credit_limit <= 0) {
            return; // No credit limit set
        }

        // Calculate current outstanding debt
        $currentDebt = Invoice::where('kodecust', $this->kodecust)
            ->where('tipe', 'CREDIT')
            ->where('status', '!=', 'Cancelled');

        if ($this->excludeInvoice) {
            $currentDebt->where('noinvoice', '!=', $this->excludeInvoice);
        }

        $currentDebt = $currentDebt->sum('saldo');
        $totalDebt = $currentDebt + $value;

        if ($totalDebt > $customer->credit_limit) {
            $fail("Credit limit exceeded. Limit: {$customer->credit_limit}, Current debt: {$currentDebt}, Proposed: {$value}");
        }
    }
}
