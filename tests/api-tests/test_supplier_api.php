<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\MSupplier;

echo "Testing MSupplier model API...\n\n";

// Test model
echo "Model table: " . (new MSupplier())->getTable() . "\n";

// Get count via model
$count = MSupplier::count();
echo "Total records via model: {$count}\n\n";

// Test if model can retrieve data
echo "Sample data via MSupplier model:\n";
try {
    $suppliers = MSupplier::take(3)->get();
    foreach ($suppliers as $supplier) {
        echo "- Divisi: {$supplier->kodedivisi}, Kode: {$supplier->kodesupplier}, Nama: {$supplier->namasupplier}\n";
    }
    
    echo "\nAPI test - simulating /api/supplier:\n";
    $apiResult = MSupplier::take(3)->get()->toArray();
    echo json_encode($apiResult, JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "This might be due to composite key handling\n";
}

echo "\nDone.\n";
