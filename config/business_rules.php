<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Business Rules Configuration
    |--------------------------------------------------------------------------
    */

    // Transaction limits
    'max_transaction_amount' => env('MAX_TRANSACTION_AMOUNT', 50000000), // 50M
    'max_invoice_items' => env('MAX_INVOICE_ITEMS', 100),
    'max_stock_transaction_items' => env('MAX_STOCK_TRANSACTION_ITEMS', 100),

    // Stock management
    'allow_negative_stock' => env('ALLOW_NEGATIVE_STOCK', false),
    'stock_warning_threshold' => env('STOCK_WARNING_THRESHOLD', 10),
    'low_stock_notification' => env('LOW_STOCK_NOTIFICATION', true),

    // Credit management
    'default_credit_limit' => env('DEFAULT_CREDIT_LIMIT', 1000000), // 1M
    'credit_limit_check' => env('CREDIT_LIMIT_CHECK', true),
    'allow_credit_exceed' => env('ALLOW_CREDIT_EXCEED', false),

    // Pricing rules
    'allow_below_minimum_price' => env('ALLOW_BELOW_MINIMUM_PRICE', false),
    'price_deviation_tolerance' => env('PRICE_DEVIATION_TOLERANCE', 0.05), // 5%
    'require_price_approval' => env('REQUIRE_PRICE_APPROVAL', true),

    // Date validation
    'max_transaction_days_back' => env('MAX_TRANSACTION_DAYS_BACK', 365),
    'allow_future_transactions' => env('ALLOW_FUTURE_TRANSACTIONS', false),
    'period_closing_enabled' => env('PERIOD_CLOSING_ENABLED', true),

    // Invoice validation
    'invoice_number_format' => env('INVOICE_NUMBER_FORMAT', 'INV-{YEAR}-{MONTH}-{SEQUENCE}'),
    'duplicate_invoice_check' => env('DUPLICATE_INVOICE_CHECK', true),
    'invoice_approval_required' => env('INVOICE_APPROVAL_REQUIRED', false),

    // Stock transaction validation
    'stock_reference_format' => env('STOCK_REFERENCE_FORMAT', 'STK-{YEAR}-{MONTH}-{SEQUENCE}'),
    'inter_company_transfer_approval' => env('INTER_COMPANY_TRANSFER_APPROVAL', true),
    'auto_stock_adjustment' => env('AUTO_STOCK_ADJUSTMENT', false),

    // Journal validation
    'journal_balance_tolerance' => env('JOURNAL_BALANCE_TOLERANCE', 0.01), // 1 cent
    'require_journal_approval' => env('REQUIRE_JOURNAL_APPROVAL', false),
    'auto_journal_numbering' => env('AUTO_JOURNAL_NUMBERING', true),

    // Security settings
    'enable_audit_trail' => env('ENABLE_AUDIT_TRAIL', true),
    'log_all_changes' => env('LOG_ALL_CHANGES', true),
    'require_approval_above_amount' => env('REQUIRE_APPROVAL_ABOVE_AMOUNT', 10000000), // 10M

    // Notification settings
    'notify_low_stock' => env('NOTIFY_LOW_STOCK', true),
    'notify_credit_limit' => env('NOTIFY_CREDIT_LIMIT', true),
    'notify_large_transactions' => env('NOTIFY_LARGE_TRANSACTIONS', true),

    // Validation rules
    'validation_rules' => [
        'invoice' => [
            'require_customer' => true,
            'require_sales_person' => true,
            'validate_stock' => true,
            'validate_pricing' => true,
            'validate_credit' => true,
        ],
        'stock_transaction' => [
            'validate_availability' => true,
            'require_reference' => true,
            'validate_transfer_rules' => true,
            'check_period_closing' => true,
        ],
        'journal' => [
            'require_balance' => true,
            'validate_coa' => true,
            'check_duplicates' => true,
            'require_description' => true,
        ],
    ],

    // Error messages
    'error_messages' => [
        'stock_insufficient' => 'Insufficient stock available',
        'credit_limit_exceeded' => 'Customer credit limit exceeded',
        'price_below_minimum' => 'Price is below minimum allowed',
        'future_date_not_allowed' => 'Future transaction dates are not allowed',
        'period_closed' => 'Cannot create transactions in closed period',
        'duplicate_reference' => 'Reference number already exists',
        'journal_not_balanced' => 'Journal entry must be balanced',
        'invalid_coa' => 'Invalid chart of accounts code',
    ],
];
