<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use GuzzleHttp\Client;

$client = new Client(['base_uri' => 'http://localhost:8000/api/']);

echo "=== TESTING JOURNAL & PENERIMAAN FINANCE APIs ===\n\n";

try {
    // Test Journal API (Limited)
    echo "1. Testing Journal API (Limited to 10 records):\n";
    echo "   GET /api/journals\n";
    $response = $client->get('journals');
    $data = json_decode($response->getBody(), true);
    
    if ($data['success']) {
        echo "   ✅ Status: " . $data['message'] . "\n";
        echo "   📊 Records shown: " . $data['total_shown'] . "\n";
        echo "   📝 Note: " . $data['note'] . "\n";
        
        if (!empty($data['data'])) {
            $firstRecord = $data['data'][0];
            echo "   📋 Sample data: ID " . ($firstRecord['id'] ?? 'N/A') . 
                 ", Tanggal: " . ($firstRecord['tanggal'] ?? 'N/A') . 
                 ", Transaksi: " . ($firstRecord['transaksi'] ?? 'N/A') . "\n";
        }
    } else {
        echo "   ❌ Error: " . $data['message'] . "\n";
    }
    
    echo "\n";

    // Test Journal API (All for Frontend)
    echo "2. Testing Journal API (All records for Frontend):\n";
    echo "   GET /api/journals/all\n";
    $response = $client->get('journals/all');
    $data = json_decode($response->getBody(), true);
    
    if ($data['success']) {
        echo "   ✅ Status: " . $data['message'] . "\n";
        echo "   📊 Total records: " . $data['total_records'] . "\n";
    } else {
        echo "   ❌ Error: " . $data['message'] . "\n";
    }
    
    echo "\n";

    // Test Penerimaan Finance API (Limited)
    echo "3. Testing Penerimaan Finance API (Limited to 5 records):\n";
    echo "   GET /api/penerimaan-finance\n";
    $response = $client->get('penerimaan-finance');
    $data = json_decode($response->getBody(), true);
    
    if ($data['success']) {
        echo "   ✅ Status: " . $data['message'] . "\n";
        echo "   📊 Records shown: " . $data['total_shown'] . "\n";
        echo "   📝 Note: " . $data['note'] . "\n";
        
        if (!empty($data['data'])) {
            $firstRecord = $data['data'][0];
            echo "   📋 Sample data: No: " . ($firstRecord['nopenerimaan'] ?? 'N/A') . 
                 ", Tanggal: " . ($firstRecord['tglpenerimaan'] ?? 'N/A') . 
                 ", Customer: " . ($firstRecord['kodecust'] ?? 'N/A') . "\n";
        }
    } else {
        echo "   ❌ Error: " . $data['message'] . "\n";
    }
    
    echo "\n";

    // Test Penerimaan Finance API (All for Frontend)
    echo "4. Testing Penerimaan Finance API (All records for Frontend):\n";
    echo "   GET /api/penerimaan-finance/all\n";
    $response = $client->get('penerimaan-finance/all');
    $data = json_decode($response->getBody(), true);
    
    if ($data['success']) {
        echo "   ✅ Status: " . $data['message'] . "\n";
        echo "   📊 Total records: " . $data['total_records'] . "\n";
    } else {
        echo "   ❌ Error: " . $data['message'] . "\n";
    }

} catch (Exception $e) {
    echo "❌ Error connecting to API: " . $e->getMessage() . "\n";
    echo "💡 Make sure Laravel server is running: php artisan serve\n";
}

echo "\n=== TESTING COMPLETED ===\n";
echo "\n📋 SUMMARY:\n";
echo "• Journal API (Laravel): Limited to 10 records for testing\n";
echo "• Journal API (Frontend): Returns all records via /journals/all\n";
echo "• Penerimaan Finance API (Laravel): Limited to 5 records for testing\n";
echo "• Penerimaan Finance API (Frontend): Returns all records via /penerimaan-finance/all\n";
echo "\n🎯 Frontend should use '/all' endpoints to display complete data!\n";
