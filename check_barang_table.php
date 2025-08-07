<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\MBarang;
use Illuminate\Support\Facades\DB;

echo "Checking barang table and data...\n\n";

// Check current model table
echo "Current model table: " . (new MBarang())->getTable() . "\n\n";

// Check if m_barang table exists and has data
echo "Checking m_barang table:\n";
try {
    $count = DB::table('m_barang')->count();
    echo "Records in m_barang: {$count}\n";
    
    if ($count > 0) {
        $sample = DB::table('m_barang')->take(3)->get();
        echo "Sample data from m_barang:\n";
        foreach ($sample as $item) {
            echo "- " . json_encode($item) . "\n";
        }
    }
} catch (Exception $e) {
    echo "Error accessing m_barang: " . $e->getMessage() . "\n";
}

echo "\n";

// Check if d_barang table exists (without schema prefix)
echo "Checking d_barang table:\n";
try {
    $count = DB::table('d_barang')->count();
    echo "Records in d_barang: {$count}\n";
    
    if ($count > 0) {
        $sample = DB::table('d_barang')->take(3)->get();
        echo "Sample data from d_barang:\n";
        foreach ($sample as $item) {
            echo "- " . json_encode($item) . "\n";
        }
    }
} catch (Exception $e) {
    echo "Error accessing d_barang: " . $e->getMessage() . "\n";
}

echo "\nDone.\n";
