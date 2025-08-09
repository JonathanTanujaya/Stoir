<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use GuzzleHttp\Client;

$client = new Client(['base_uri' => 'http://localhost:8000/api/']);

echo "=== QUICK TEST KARTU STOK API ===\n\n";

try {
    echo "Testing Kartu Stok API:\n";
    echo "GET /api/kartu-stok\n";
    
    $response = $client->get('kartu-stok');
    $data = json_decode($response->getBody(), true);
    
    if ($data['success']) {
        echo "✅ SUCCESS: " . $data['message'] . "\n";
        echo "📊 Records: " . $data['total_shown'] . "\n";
        echo "📝 Note: " . $data['note'] . "\n";
        
        if (!empty($data['data'])) {
            echo "📋 First record structure:\n";
            $first = $data['data'][0];
            foreach ($first as $key => $value) {
                echo "   - $key: " . (is_null($value) ? 'NULL' : $value) . "\n";
            }
        }
    } else {
        echo "❌ FAILED: " . $data['message'] . "\n";
        if (isset($data['error'])) {
            echo "🐛 Error: " . $data['error'] . "\n";
        }
    }

} catch (Exception $e) {
    echo "❌ Connection Error: " . $e->getMessage() . "\n";
    echo "💡 Make sure Laravel server is running\n";
}

echo "\n=== TEST COMPLETED ===\n";
