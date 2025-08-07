<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\PenerimaanFinance;
use App\Models\SaldoBank;
use App\Models\MResi;

echo "Testing newly created models...\n\n";

// Test PenerimaanFinance
echo "=== PenerimaanFinance Model ===\n";
try {
    $count = PenerimaanFinance::count();
    echo "Total records: {$count}\n";
    
    $sample = PenerimaanFinance::first();
    if ($sample) {
        echo "Sample data: " . json_encode($sample->toArray()) . "\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== SaldoBank Model ===\n";
try {
    $count = SaldoBank::count();
    echo "Total records: {$count}\n";
    
    $sample = SaldoBank::first();
    if ($sample) {
        echo "Sample data: " . json_encode($sample->toArray()) . "\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== MResi Model ===\n";
try {
    $count = MResi::count();
    echo "Total records: {$count}\n";
    
    $sample = MResi::first();
    if ($sample) {
        echo "Sample data: " . json_encode($sample->toArray()) . "\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\nDone.\n";
