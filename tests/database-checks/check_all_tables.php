<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Checking available tables and data...\n\n";

try {
    echo "=== AVAILABLE TABLES WITH DATA ===\n";
    
    // Check m_cust
    $custCount = \DB::table('dbo.m_cust')->count();
    echo "dbo.m_cust: {$custCount} records\n";
    
    // Check m_sales  
    $salesCount = \DB::table('dbo.m_sales')->count();
    echo "dbo.m_sales: {$salesCount} records\n";
    
    // Check d_barang
    $dBarangCount = \DB::table('dbo.d_barang')->count();
    echo "dbo.d_barang: {$dBarangCount} records\n";
    
    // Check if there are other customer/sales tables
    echo "\n=== SEARCHING FOR CUSTOMER TABLES ===\n";
    $customerTables = \DB::select("SELECT table_name FROM information_schema.tables WHERE table_schema = 'dbo' AND table_name LIKE '%cust%'");
    foreach ($customerTables as $table) {
        $count = \DB::table("dbo.{$table->table_name}")->count();
        echo "dbo.{$table->table_name}: {$count} records\n";
    }
    
    echo "\n=== SEARCHING FOR SALES TABLES ===\n";
    $salesTables = \DB::select("SELECT table_name FROM information_schema.tables WHERE table_schema = 'dbo' AND table_name LIKE '%sales%'");
    foreach ($salesTables as $table) {
        $count = \DB::table("dbo.{$table->table_name}")->count();
        echo "dbo.{$table->table_name}: {$count} records\n";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\nDone.\n";
