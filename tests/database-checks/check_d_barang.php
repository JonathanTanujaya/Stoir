<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Checking dbo.d_barang table structure...\n\n";

try {
    // Check d_barang table structure
    echo "=== DBO.D_BARANG TABLE STRUCTURE ===\n";
    $barangColumns = \DB::select("SELECT column_name, data_type FROM information_schema.columns WHERE table_schema = 'dbo' AND table_name = 'd_barang' ORDER BY ordinal_position");
    foreach ($barangColumns as $column) {
        echo "- {$column->column_name} ({$column->data_type})\n";
    }
    
    echo "\n=== SAMPLE DATA FROM DBO.D_BARANG ===\n";
    $barangSample = \DB::table('dbo.d_barang')->limit(5)->get();
    if ($barangSample->count() > 0) {
        echo "Found {$barangSample->count()} records\n";
        foreach ($barangSample->first() as $key => $value) {
            echo "  {$key}: {$value}\n";
        }
    } else {
        echo "  No data found\n";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\nDone.\n";
