<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Carbon\Carbon;

class NotFutureDate implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $date = Carbon::parse($value);
        $now = Carbon::now();

        if ($date->isFuture()) {
            $fail('The transaction date cannot be in the future.');
        }

        // Check if date is too far in the past
        $maxDaysBack = config('business_rules.max_transaction_days_back', 365);
        if ($date->diffInDays($now) > $maxDaysBack) {
            $fail("The transaction date cannot be more than {$maxDaysBack} days old.");
        }
    }
}
