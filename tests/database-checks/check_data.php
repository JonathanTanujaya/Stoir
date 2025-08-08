<?php

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';

// Check customer data
echo "=== CUSTOMER DATA CHECK ===\n";
try {
    $customerCount = App\Models\MCust::count();
    echo "Total customers: {$customerCount}\n";
    
    if ($customerCount > 0) {
        $customers = App\Models\MCust::take(5)->get(['kodedivisi', 'kodecust', 'namacust']);
        echo "Sample customers:\n";
        foreach ($customers as $customer) {
            echo "- {$customer->kodedivisi}/{$customer->kodecust} - {$customer->namacust}\n";
        }
    } else {
        echo "No customers found in database.\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// Check sales data
echo "\n=== SALES DATA CHECK ===\n";
try {
    $salesCount = App\Models\MSales::count();
    echo "Total sales: {$salesCount}\n";
    
    if ($salesCount > 0) {
        $sales = App\Models\MSales::take(5)->get(['kodedivisi', 'kodesales', 'namasales']);
        echo "Sample sales:\n";
        foreach ($sales as $sale) {
            echo "- {$sale->kodedivisi}/{$sale->kodesales} - {$sale->namasales}\n";
        }
    } else {
        echo "No sales found in database.\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\nDone.\n";
