<?php

/**
 * Script testing untuk BarangController API
 * Pastikan Laravel development server sudah berjalan di localhost:8000
 */

$baseUrl = 'http://localhost:8000/api';
$divisi = 'TEST';

function makeRequest($method, $url, $data = null) {
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json'
    ]);
    
    if ($data && in_array($method, ['POST', 'PUT', 'PATCH'])) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return [
        'status_code' => $httpCode,
        'response' => json_decode($response, true),
        'raw_response' => $response
    ];
}

echo "=== TESTING BARANG CRUD API ===\n\n";

// 1. TEST GET - List all barangs
echo "1. Testing GET /api/divisi/{$divisi}/barangs\n";
$result = makeRequest('GET', "{$baseUrl}/divisi/{$divisi}/barangs");
echo "Status: {$result['status_code']}\n";
if ($result['status_code'] == 200) {
    echo "✓ SUCCESS - Found " . count($result['response']['data']) . " barangs\n";
    foreach ($result['response']['data'] as $barang) {
        echo "  - {$barang['kode_barang']}: {$barang['nama_barang']}\n";
    }
} else {
    echo "✗ FAILED\n";
    echo "Response: " . $result['raw_response'] . "\n";
}
echo "\n";

// 2. TEST GET - Search barangs
echo "2. Testing GET with search parameter\n";
$result = makeRequest('GET', "{$baseUrl}/divisi/{$divisi}/barangs?search=laptop");
echo "Status: {$result['status_code']}\n";
if ($result['status_code'] == 200) {
    echo "✓ SUCCESS - Search found " . count($result['response']['data']) . " items\n";
} else {
    echo "✗ FAILED\n";
}
echo "\n";

// 3. TEST GET - Show specific barang
echo "3. Testing GET /api/divisi/{$divisi}/barangs/BRG001\n";
$result = makeRequest('GET', "{$baseUrl}/divisi/{$divisi}/barangs/BRG001");
echo "Status: {$result['status_code']}\n";
if ($result['status_code'] == 200) {
    echo "✓ SUCCESS - Retrieved: {$result['response']['nama_barang']}\n";
} else {
    echo "✗ FAILED\n";
    echo "Response: " . $result['raw_response'] . "\n";
}
echo "\n";

// 4. TEST POST - Create new barang
echo "4. Testing POST - Create new barang\n";
$newBarang = [
    'kode_barang' => 'BRG999',
    'nama_barang' => 'Test Product API',
    'kode_kategori' => 'KAT001',
    'harga_list' => 500000,
    'harga_jual' => 600000,
    'satuan' => 'PCS',
    'disc1' => 5,
    'disc2' => 2,
    'merk' => 'Test Brand',
    'status' => true,
    'lokasi' => 'TEST',
    'stok_min' => 5
];

$result = makeRequest('POST', "{$baseUrl}/divisi/{$divisi}/barangs", $newBarang);
echo "Status: {$result['status_code']}\n";
if ($result['status_code'] == 201) {
    echo "✓ SUCCESS - Created: {$result['response']['nama_barang']}\n";
} else {
    echo "✗ FAILED\n";
    echo "Response: " . $result['raw_response'] . "\n";
}
echo "\n";

// 5. TEST PUT - Update barang
echo "5. Testing PUT - Update barang BRG999\n";
$updateData = [
    'nama_barang' => 'Updated Test Product API',
    'harga_jual' => 650000,
    'merk' => 'Updated Brand'
];

$result = makeRequest('PUT', "{$baseUrl}/divisi/{$divisi}/barangs/BRG999", $updateData);
echo "Status: {$result['status_code']}\n";
if ($result['status_code'] == 200) {
    echo "✓ SUCCESS - Updated: {$result['response']['nama_barang']}\n";
} else {
    echo "✗ FAILED\n";
    echo "Response: " . $result['raw_response'] . "\n";
}
echo "\n";

// 6. TEST GET - Categories
echo "6. Testing GET - Categories\n";
$result = makeRequest('GET', "{$baseUrl}/divisi/{$divisi}/categories");
echo "Status: {$result['status_code']}\n";
if ($result['status_code'] == 200) {
    echo "✓ SUCCESS - Found " . count($result['response']) . " categories\n";
} else {
    echo "✗ FAILED\n";
    echo "Response: " . $result['raw_response'] . "\n";
}
echo "\n";

// 7. TEST GET - Stock info
echo "7. Testing GET - Stock info for BRG001\n";
$result = makeRequest('GET', "{$baseUrl}/divisi/{$divisi}/barangs/BRG001/stock-info");
echo "Status: {$result['status_code']}\n";
if ($result['status_code'] == 200) {
    echo "✓ SUCCESS - Retrieved stock info\n";
} else {
    echo "✗ FAILED\n";
    echo "Response: " . $result['raw_response'] . "\n";
}
echo "\n";

// 8. TEST Validation - Create barang with missing data
echo "8. Testing Validation - Create barang with missing required fields\n";
$invalidData = [
    'nama_barang' => 'Invalid Product'
    // Missing required fields: kode_barang, kode_kategori
];

$result = makeRequest('POST', "{$baseUrl}/divisi/{$divisi}/barangs", $invalidData);
echo "Status: {$result['status_code']}\n";
if ($result['status_code'] == 422) {
    echo "✓ SUCCESS - Validation working correctly\n";
    echo "Validation errors: " . json_encode($result['response']['errors']) . "\n";
} else {
    echo "✗ FAILED - Validation should return 422\n";
}
echo "\n";

// 9. TEST DELETE - Delete barang
echo "9. Testing DELETE - Delete barang BRG999\n";
$result = makeRequest('DELETE', "{$baseUrl}/divisi/{$divisi}/barangs/BRG999");
echo "Status: {$result['status_code']}\n";
if ($result['status_code'] == 200) {
    echo "✓ SUCCESS - Deleted barang\n";
} else {
    echo "✗ FAILED\n";
    echo "Response: " . $result['raw_response'] . "\n";
}
echo "\n";

echo "=== TESTING COMPLETED ===\n";
