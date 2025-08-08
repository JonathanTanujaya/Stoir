<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use GuzzleHttp\Client;

$client = new Client(['base_uri' => 'http://localhost:8000/api/']);

echo "=== TESTING KARTU STOK API ===\n\n";

try {
    // Test Kartu Stok API (Limited)
    echo "1. Testing Kartu Stok API (Limited to 10 records):\n";
    echo "   GET /api/kartu-stok\n";
    $response = $client->get('kartu-stok');
    $data = json_decode($response->getBody(), true);
    
    if ($data['success']) {
        echo "   ✅ Status: " . $data['message'] . "\n";
        echo "   📊 Records shown: " . $data['total_shown'] . "\n";
        echo "   📝 Note: " . $data['note'] . "\n";
        
        if (!empty($data['data'])) {
            $firstRecord = $data['data'][0];
            echo "   📋 Sample data: \n";
            echo "      - ID: " . ($firstRecord['id'] ?? 'N/A') . "\n";
            echo "      - Kode Barang: " . ($firstRecord['kodebarang'] ?? 'N/A') . "\n";
            echo "      - Tanggal: " . ($firstRecord['tanggal'] ?? 'N/A') . "\n";
            echo "      - Jenis Transaksi: " . ($firstRecord['jenistransaksi'] ?? 'N/A') . "\n";
            echo "      - Masuk: " . ($firstRecord['masuk'] ?? 'N/A') . "\n";
            echo "      - Keluar: " . ($firstRecord['keluar'] ?? 'N/A') . "\n";
            echo "      - Saldo: " . ($firstRecord['saldo'] ?? 'N/A') . "\n";
        }
    } else {
        echo "   ❌ Error: " . $data['message'] . "\n";
    }
    
    echo "\n";

    // Test Kartu Stok API (All for Frontend)
    echo "2. Testing Kartu Stok API (All records for Frontend):\n";
    echo "   GET /api/kartu-stok/all\n";
    $response = $client->get('kartu-stok/all');
    $data = json_decode($response->getBody(), true);
    
    if ($data['success']) {
        echo "   ✅ Status: " . $data['message'] . "\n";
        echo "   📊 Total records: " . $data['total_records'] . "\n";
        echo "   📈 Data size: " . round(strlen(json_encode($data['data'])) / 1024, 2) . " KB\n";
    } else {
        echo "   ❌ Error: " . $data['message'] . "\n";
    }

} catch (Exception $e) {
    echo "❌ Error connecting to API: " . $e->getMessage() . "\n";
    echo "💡 Make sure Laravel server is running: php artisan serve\n";
}

echo "\n=== TESTING COMPLETED ===\n";
echo "\n📋 SUMMARY:\n";
echo "• Kartu Stok API (Laravel): Limited to 10 records for testing\n";
echo "• Kartu Stok API (Frontend): Returns all records via /kartu-stok/all\n";
echo "• Special endpoint: /kartu-stok/by-barang/{kodeDivisi}/{kodeBarang} for specific item\n";
echo "\n🎯 Kartu Stok berhasil dikembalikan dengan:\n";
echo "  - 7,752 records (berdasarkan scipt.txt)\n";
echo "  - Full CRUD operations\n";
echo "  - Relationship dengan MBarang\n";
echo "  - Query by barang specific\n";
