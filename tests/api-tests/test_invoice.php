<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Invoice;

echo "=== TESTING INVOICE MODEL ===\n\n";

try {
    // Test simple query
    $invoices = Invoice::orderBy('tglfaktur', 'desc')->take(1)->get();
    echo "Success: " . $invoices->count() . " invoices found\n";
    
    if ($invoices->count() > 0) {
        $invoice = $invoices->first();
        echo "Sample invoice: " . $invoice->noinvoice . " - " . $invoice->kodecust . "\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== END TEST ===\n";
