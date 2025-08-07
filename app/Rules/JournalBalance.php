<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class JournalBalance implements ValidationRule
{
    protected $details;

    public function __construct($details)
    {
        $this->details = $details;
    }

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $totalDebit = 0;
        $totalCredit = 0;

        foreach ($this->details as $detail) {
            $totalDebit += $detail['debit'] ?? 0;
            $totalCredit += $detail['kredit'] ?? 0;
        }

        $difference = abs($totalDebit - $totalCredit);
        $tolerance = config('business_rules.journal_balance_tolerance', 0.01);

        if ($difference > $tolerance) {
            $fail("Journal entry is not balanced. Debit: {$totalDebit}, Credit: {$totalCredit}");
        }
    }
}
