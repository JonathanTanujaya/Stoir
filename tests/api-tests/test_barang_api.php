<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\MBarang;

echo "Testing MBarang model with d_barang table...\n\n";

// Test model
echo "Model table: " . (new MBarang())->getTable() . "\n";

// Get count via model
$count = MBarang::count();
echo "Total records via model: {$count}\n\n";

// Get sample data via model
echo "Sample data via MBarang model:\n";
$barang = MBarang::take(3)->get();
foreach ($barang as $item) {
    echo "- Divisi: {$item->kodedivisi}, Kode: {$item->kodebarang}, Modal: {$item->modal}, Stok: {$item->stok}\n";
}

echo "\nAPI test - simulating /api/barang:\n";
$apiResult = MBarang::take(5)->get()->toArray();
echo json_encode($apiResult, JSON_PRETTY_PRINT);

echo "\nDone.\n";
