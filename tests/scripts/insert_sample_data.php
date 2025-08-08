<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Creating sample master data with direct SQL...\n\n";

try {
    // Clear existing data first
    echo "Clearing existing data...\n";
    \DB::statement('DELETE FROM m_cust');
    \DB::statement('DELETE FROM m_sales');  
    \DB::statement('DELETE FROM m_barang');
    echo "✅ Existing data cleared\n\n";
    
    // Insert customers
    echo "=== CREATING CUSTOMERS ===\n";
    $custSql = 'INSERT INTO m_cust ("kodedivisi", "kodecust", "namacust", "kodearea", "alamat", "telp", "contact", "creditlimit", "jatuhtempo", "status") VALUES 
        (\'01\', \'CUST001\', \'PT Maju Jaya\', \'AREA01\', \'Jl. Merdeka No. 123\', \'021-1234567\', \'Budi Manager\', 10000000, 30, true),
        (\'01\', \'CUST002\', \'CV Sejahtera\', \'AREA01\', \'Jl. Sudirman No. 456\', \'021-2345678\', \'Siti Director\', 15000000, 45, true),
        (\'01\', \'CUST003\', \'Toko Sumber Rezeki\', \'AREA02\', \'Jl. Thamrin No. 789\', \'021-3456789\', \'Ahmad Owner\', 8000000, 30, true)';
    
    \DB::statement($custSql);
    echo "✅ 3 customers created\n";
    
    // Insert sales
    echo "\n=== CREATING SALES ===\n";
    $salesSql = 'INSERT INTO m_sales ("kodedivisi", "kodesales", "namasales", "alamat", "nohp", "target", "status") VALUES 
        (\'01\', \'SAL001\', \'John Doe\', \'Jl. Gatot Subroto No. 100\', \'081234567890\', 50000000, true),
        (\'01\', \'SAL002\', \'Jane Smith\', \'Jl. HR Rasuna Said No. 200\', \'081345678901\', 60000000, true),
        (\'01\', \'SAL003\', \'Robert Johnson\', \'Jl. Kuningan No. 300\', \'081456789012\', 45000000, true)';
    
    \DB::statement($salesSql);
    echo "✅ 3 sales created\n";
    
    // Insert barang
    echo "\n=== CREATING BARANG ===\n";
    $barangSql = 'INSERT INTO m_barang ("kodedivisi", "kodebarang", "namabarang", "kodekategori", "hargalist", "hargajual", "satuan", "disc1", "disc2", "merk", "barcode", "status", "lokasi", "stokmin") VALUES 
        (\'01\', \'BRG001\', \'Laptop Dell Inspiron 15\', \'LAPTOP\', 8500000, 8000000, \'Unit\', 0.05, 0.02, \'Dell\', \'1234567890001\', true, \'Gudang A\', 5),
        (\'01\', \'BRG002\', \'Mouse Logitech M100\', \'AKSESORIS\', 150000, 135000, \'Pcs\', 0.10, 0.05, \'Logitech\', \'1234567890002\', true, \'Gudang A\', 20),
        (\'01\', \'BRG003\', \'Keyboard Mechanical RGB\', \'AKSESORIS\', 750000, 680000, \'Pcs\', 0.08, 0.03, \'Corsair\', \'1234567890003\', true, \'Gudang B\', 10)';
    
    \DB::statement($barangSql);
    echo "✅ 3 barang created\n";
    
    echo "\n=== VERIFICATION ===\n";
    
    // Verify data
    $customerCount = \DB::table('m_cust')->count();
    echo "Total customers: {$customerCount}\n";
    
    $salesCount = \DB::table('m_sales')->count();
    echo "Total sales: {$salesCount}\n";
    
    $barangCount = \DB::table('m_barang')->count();
    echo "Total barang: {$barangCount}\n";
    
    echo "\n✅ Sample master data creation completed successfully!\n";
    echo "\nYou can now test:\n";
    echo "- Customer Management (localhost:5174/customer)\n";
    echo "- Sales Management (localhost:5174/sales)\n";
    echo "- Barang Management (localhost:5174/barang)\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\nDone.\n";
