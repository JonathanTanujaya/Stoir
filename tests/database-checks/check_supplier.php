<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\MSupplier;

echo "=== CHECKING SUPPLIER DATA ===\n\n";

// 1. Check direct database query
echo "1. Direct Database Query:\n";
try {
    $dbSuppliers = DB::select("SELECT COUNT(*) as total FROM dbo.m_supplier");
    echo "Total records in dbo.m_supplier: " . $dbSuppliers[0]->total . "\n";
    
    if ($dbSuppliers[0]->total > 0) {
        $sampleData = DB::select("SELECT TOP 3 * FROM dbo.m_supplier");
        echo "Sample data from database:\n";
        foreach ($sampleData as $supplier) {
            echo "- {$supplier->kodedivisi} | {$supplier->kodesupplier} | {$supplier->namasupplier}\n";
        }
    }
} catch (Exception $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}

echo "\n";

// 2. Check Model query
echo "2. Model Query (MSupplier):\n";
try {
    $modelSuppliers = MSupplier::count();
    echo "Total records via MSupplier model: " . $modelSuppliers . "\n";
    
    if ($modelSuppliers > 0) {
        $sampleModelData = MSupplier::take(3)->get();
        echo "Sample data via model:\n";
        foreach ($sampleModelData as $supplier) {
            echo "- {$supplier->kodedivisi} | {$supplier->kodesupplier} | {$supplier->namasupplier}\n";
        }
    }
} catch (Exception $e) {
    echo "Model error: " . $e->getMessage() . "\n";
}

echo "\n";

// 3. Check table structure
echo "3. Table Structure Check:\n";
try {
    $columns = DB::select("SELECT COLUMN_NAME, DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = 'dbo' AND TABLE_NAME = 'm_supplier' ORDER BY ORDINAL_POSITION");
    echo "Columns in dbo.m_supplier:\n";
    foreach ($columns as $column) {
        echo "- {$column->COLUMN_NAME} ({$column->DATA_TYPE})\n";
    }
} catch (Exception $e) {
    echo "Structure check error: " . $e->getMessage() . "\n";
}

echo "\n=== END CHECK ===\n";
