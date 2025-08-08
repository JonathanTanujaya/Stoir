<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Checking database table structures...\n\n";

try {
    // Check m_cust table structure
    echo "=== M_CUST TABLE STRUCTURE ===\n";
    $custColumns = \DB::select("SELECT column_name, data_type FROM information_schema.columns WHERE table_name = 'm_cust' ORDER BY ordinal_position");
    foreach ($custColumns as $column) {
        echo "- {$column->column_name} ({$column->data_type})\n";
    }
    
    echo "\n=== M_SALES TABLE STRUCTURE ===\n";
    $salesColumns = \DB::select("SELECT column_name, data_type FROM information_schema.columns WHERE table_name = 'm_sales' ORDER BY ordinal_position");
    foreach ($salesColumns as $column) {
        echo "- {$column->column_name} ({$column->data_type})\n";
    }
    
    echo "\n=== M_BARANG TABLE STRUCTURE ===\n";
    $barangColumns = \DB::select("SELECT column_name, data_type FROM information_schema.columns WHERE table_name = 'm_barang' ORDER BY ordinal_position");
    foreach ($barangColumns as $column) {
        echo "- {$column->column_name} ({$column->data_type})\n";
    }
    
    echo "\n=== SAMPLE DATA FROM EXISTING RECORDS ===\n";
    
    // Try to get sample data to understand the structure
    echo "M_CUST sample data:\n";
    $custSample = \DB::table('m_cust')->limit(1)->get();
    if ($custSample->count() > 0) {
        foreach ($custSample->first() as $key => $value) {
            echo "  {$key}: {$value}\n";
        }
    } else {
        echo "  No data found\n";
    }
    
    echo "\nM_SALES sample data:\n";
    $salesSample = \DB::table('m_sales')->limit(1)->get();
    if ($salesSample->count() > 0) {
        foreach ($salesSample->first() as $key => $value) {
            echo "  {$key}: {$value}\n";
        }
    } else {
        echo "  No data found\n";
    }
    
    echo "\nM_BARANG sample data:\n";
    $barangSample = \DB::table('m_barang')->limit(1)->get();
    if ($barangSample->count() > 0) {
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
