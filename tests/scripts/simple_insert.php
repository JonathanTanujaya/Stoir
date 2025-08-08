<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Creating minimal sample data using uppercase columns...\n\n";

try {
    // First, let's check what columns actually exist and work
    echo "=== TESTING SIMPLE INSERT ===\n";
    
    // Try with uppercase columns only
    $custSql = 'INSERT INTO m_cust ("KodeDivisi", "KodeCust", "NamaCust") VALUES 
        (\'01\', \'C001\', \'PT Maju\')';
    
    \DB::statement($custSql);
    echo "✅ Customer test insert successful\n";
    
    $salesSql = 'INSERT INTO m_sales ("KodeDivisi", "KodeSales", "NamaSales") VALUES 
        (\'01\', \'S001\', \'John\')';
    
    \DB::statement($salesSql);
    echo "✅ Sales test insert successful\n";
    
    $barangSql = 'INSERT INTO m_barang ("KodeDivisi", "KodeBarang", "NamaBarang") VALUES 
        (\'01\', \'B001\', \'Laptop\')';
    
    \DB::statement($barangSql);
    echo "✅ Barang test insert successful\n";
    
    echo "\n=== VERIFICATION ===\n";
    
    // Verify data
    $customerCount = \DB::table('m_cust')->count();
    echo "Total customers: {$customerCount}\n";
    
    $salesCount = \DB::table('m_sales')->count();
    echo "Total sales: {$salesCount}\n";
    
    $barangCount = \DB::table('m_barang')->count();
    echo "Total barang: {$barangCount}\n";
    
    echo "\n✅ Basic sample data created! Now test the API endpoints.\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\nDone.\n";
