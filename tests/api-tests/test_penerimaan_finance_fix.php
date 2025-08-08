<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use GuzzleHttp\Client;

$client = new Client(['base_uri' => 'http://localhost:8000/api/']);

echo "=== TESTING PENERIMAAN FINANCE FIX ===\n\n";

try {
    // Test Penerimaan Finance API (Fixed)
    echo "1. Testing Penerimaan Finance API (Fixed - No Relations):\n";
    echo "   GET /api/penerimaan-finance\n";
    
    $response = $client->get('penerimaan-finance');
    $data = json_decode($response->getBody(), true);
    
    if ($data['success']) {
        echo "   ✅ Status: SUCCESS\n";
        echo "   📊 Records shown: " . $data['total_shown'] . "\n";
        echo "   📝 Message: " . $data['message'] . "\n";
        echo "   ⚠️  Note: " . ($data['note'] ?? 'No note') . "\n";
        
        if (!empty($data['data'])) {
            $firstRecord = $data['data'][0];
            echo "   📋 Sample data: \n";
            echo "      - No Penerimaan: " . ($firstRecord['nopenerimaan'] ?? 'N/A') . "\n";
            echo "      - Tanggal: " . ($firstRecord['tglpenerimaan'] ?? 'N/A') . "\n";
            echo "      - Kode Customer: " . ($firstRecord['kodecust'] ?? 'N/A') . "\n";
            echo "      - Jumlah: " . ($firstRecord['jumlah'] ?? 'N/A') . "\n";
        } else {
            echo "   ⚠️  No data found\n";
        }
    } else {
        echo "   ❌ Error: " . $data['message'] . "\n";
        if (isset($data['error'])) {
            echo "   🐛 Detail: " . $data['error'] . "\n";
        }
    }
    
    echo "\n";

    // Test All data endpoint
    echo "2. Testing Penerimaan Finance All Data:\n";
    echo "   GET /api/penerimaan-finance/all\n";
    
    $response = $client->get('penerimaan-finance/all');
    $data = json_decode($response->getBody(), true);
    
    if ($data['success']) {
        echo "   ✅ Status: SUCCESS\n";
        echo "   📊 Total records: " . $data['total_records'] . "\n";
        echo "   📝 Message: " . $data['message'] . "\n";
        echo "   ⚠️  Note: " . ($data['note'] ?? 'No note') . "\n";
    } else {
        echo "   ❌ Error: " . $data['message'] . "\n";
        if (isset($data['error'])) {
            echo "   🐛 Detail: " . $data['error'] . "\n";
        }
    }

} catch (Exception $e) {
    echo "❌ Error connecting to API: " . $e->getMessage() . "\n";
    echo "💡 Make sure Laravel server is running: php artisan serve\n";
}

echo "\n=== TESTING COMPLETED ===\n";
echo "\n📋 SUMMARY:\n";
echo "• Composite key error should be fixed\n";
echo "• Relations temporarily disabled to prevent str_contains() error\n";
echo "• Basic data retrieval should work now\n";
echo "• Relations can be re-enabled later with proper implementation\n";
