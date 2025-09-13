<?php

require_once 'vendor/autoload.php';

/**
 * Testing script for DBarang API endpoints
 * 
 * This script tests the CRUD operations for barang detail management API
 * 
 * Endpoints:
 * - GET /api/divisi/{kodeDivisi}/barangs/{kodeBarang}/details (list barang details)
 * - POST /api/divisi/{kodeDivisi}/barangs/{kodeBarang}/details (create barang detail)
 * - GET /api/divisi/{kodeDivisi}/barangs/{kodeBarang}/details/{id} (show barang detail)
 * - PUT /api/divisi/{kodeDivisi}/barangs/{kodeBarang}/details/{id} (update barang detail)
 * - DELETE /api/divisi/{kodeDivisi}/barangs/{kodeBarang}/details/{id} (delete barang detail)
 * - GET /api/divisi/{kodeDivisi}/barangs/{kodeBarang}/details-statistics (barang detail statistics)
 */

// API Base Configuration
$baseUrl = 'http://localhost:8000/api';
$authToken = 'your-auth-token-here'; // Replace with actual Sanctum token

// Test data
$testKodeDivisi = 'DIV01';
$testKodeBarang = 'BRG001';
$testDetailId = null; // Will be set after creation

/**
 * Make HTTP request with cURL
 */
function makeRequest($url, $method = 'GET', $data = null, $token = null) {
    $ch = curl_init();
    
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Accept: application/json',
            $token ? 'Authorization: Bearer ' . $token : ''
        ],
    ]);
    
    if ($data && in_array($method, ['POST', 'PUT', 'PATCH'])) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return [
        'status' => $httpCode,
        'body' => json_decode($response, true),
        'raw' => $response
    ];
}

echo "=== TESTING DBARANG API ===\n\n";

// Test 1: Create Barang Detail
echo "1. Testing CREATE Barang Detail\n";
echo "POST {$baseUrl}/divisi/{$testKodeDivisi}/barangs/{$testKodeBarang}/details\n";

$createData = [
    'tgl_masuk' => '2024-01-15',
    'modal' => 125000.75,
    'stok' => 50
];

$response = makeRequest(
    "{$baseUrl}/divisi/{$testKodeDivisi}/barangs/{$testKodeBarang}/details", 
    'POST', 
    $createData,
    $authToken
);

echo "Status: {$response['status']}\n";
echo "Response: " . json_encode($response['body'], JSON_PRETTY_PRINT) . "\n\n";

// Extract ID if successful
if ($response['status'] == 201 && isset($response['body']['data']['id'])) {
    $testDetailId = $response['body']['data']['id'];
    echo "Created Detail ID: {$testDetailId}\n\n";
}

// Test 2: Get Barang Details List
echo "2. Testing GET Barang Details List\n";
echo "GET {$baseUrl}/divisi/{$testKodeDivisi}/barangs/{$testKodeBarang}/details\n";

$response = makeRequest(
    "{$baseUrl}/divisi/{$testKodeDivisi}/barangs/{$testKodeBarang}/details",
    'GET',
    null,
    $authToken
);

echo "Status: {$response['status']}\n";
echo "Response: " . json_encode($response['body'], JSON_PRETTY_PRINT) . "\n\n";

// Test 3: Get Barang Details List with Filters
echo "3. Testing GET Barang Details List with Filters\n";
echo "GET {$baseUrl}/divisi/{$testKodeDivisi}/barangs/{$testKodeBarang}/details?available_only=true&modal_min=100000\n";

$response = makeRequest(
    "{$baseUrl}/divisi/{$testKodeDivisi}/barangs/{$testKodeBarang}/details?available_only=true&modal_min=100000",
    'GET',
    null,
    $authToken
);

echo "Status: {$response['status']}\n";
echo "Response: " . json_encode($response['body'], JSON_PRETTY_PRINT) . "\n\n";

// Test 4: Get Specific Barang Detail
if ($testDetailId) {
    echo "4. Testing GET Specific Barang Detail\n";
    echo "GET {$baseUrl}/divisi/{$testKodeDivisi}/barangs/{$testKodeBarang}/details/{$testDetailId}\n";

    $response = makeRequest(
        "{$baseUrl}/divisi/{$testKodeDivisi}/barangs/{$testKodeBarang}/details/{$testDetailId}",
        'GET',
        null,
        $authToken
    );

    echo "Status: {$response['status']}\n";
    echo "Response: " . json_encode($response['body'], JSON_PRETTY_PRINT) . "\n\n";
} else {
    echo "4. Skipping GET Specific Barang Detail (no ID available)\n\n";
}

// Test 5: Update Barang Detail
if ($testDetailId) {
    echo "5. Testing UPDATE Barang Detail\n";
    echo "PUT {$baseUrl}/divisi/{$testKodeDivisi}/barangs/{$testKodeBarang}/details/{$testDetailId}\n";

    $updateData = [
        'modal' => 135000.00,
        'stok' => 45
    ];

    $response = makeRequest(
        "{$baseUrl}/divisi/{$testKodeDivisi}/barangs/{$testKodeBarang}/details/{$testDetailId}",
        'PUT',
        $updateData,
        $authToken
    );

    echo "Status: {$response['status']}\n";
    echo "Response: " . json_encode($response['body'], JSON_PRETTY_PRINT) . "\n\n";
} else {
    echo "5. Skipping UPDATE Barang Detail (no ID available)\n\n";
}

// Test 6: Get Barang Detail Statistics
echo "6. Testing GET Barang Detail Statistics\n";
echo "GET {$baseUrl}/divisi/{$testKodeDivisi}/barangs/{$testKodeBarang}/details-statistics\n";

$response = makeRequest(
    "{$baseUrl}/divisi/{$testKodeDivisi}/barangs/{$testKodeBarang}/details-statistics",
    'GET',
    null,
    $authToken
);

echo "Status: {$response['status']}\n";
echo "Response: " . json_encode($response['body'], JSON_PRETTY_PRINT) . "\n\n";

// Test 7: Create Barang Detail with Invalid Data (Validation Test)
echo "7. Testing CREATE Barang Detail with Invalid Data\n";
echo "POST {$baseUrl}/divisi/{$testKodeDivisi}/barangs/{$testKodeBarang}/details\n";

$invalidData = [
    'modal' => -1000, // Negative modal
    'stok' => -10,    // Negative stok
    'tgl_masuk' => 'invalid-date'
];

$response = makeRequest(
    "{$baseUrl}/divisi/{$testKodeDivisi}/barangs/{$testKodeBarang}/details",
    'POST',
    $invalidData,
    $authToken
);

echo "Status: {$response['status']}\n";
echo "Response: " . json_encode($response['body'], JSON_PRETTY_PRINT) . "\n\n";

// Test 8: Get Barang Details for Non-existent Division
echo "8. Testing GET Barang Details for Non-existent Division\n";
echo "GET {$baseUrl}/divisi/INVALID/barangs/{$testKodeBarang}/details\n";

$response = makeRequest(
    "{$baseUrl}/divisi/INVALID/barangs/{$testKodeBarang}/details",
    'GET',
    null,
    $authToken
);

echo "Status: {$response['status']}\n";
echo "Response: " . json_encode($response['body'], JSON_PRETTY_PRINT) . "\n\n";

// Test 9: Get Barang Details for Non-existent Product
echo "9. Testing GET Barang Details for Non-existent Product\n";
echo "GET {$baseUrl}/divisi/{$testKodeDivisi}/barangs/INVALID/details\n";

$response = makeRequest(
    "{$baseUrl}/divisi/{$testKodeDivisi}/barangs/INVALID/details",
    'GET',
    null,
    $authToken
);

echo "Status: {$response['status']}\n";
echo "Response: " . json_encode($response['body'], JSON_PRETTY_PRINT) . "\n\n";

// Test 10: Delete Barang Detail
if ($testDetailId) {
    echo "10. Testing DELETE Barang Detail\n";
    echo "DELETE {$baseUrl}/divisi/{$testKodeDivisi}/barangs/{$testKodeBarang}/details/{$testDetailId}\n";

    $response = makeRequest(
        "{$baseUrl}/divisi/{$testKodeDivisi}/barangs/{$testKodeBarang}/details/{$testDetailId}",
        'DELETE',
        null,
        $authToken
    );

    echo "Status: {$response['status']}\n";
    echo "Response: " . json_encode($response['body'], JSON_PRETTY_PRINT) . "\n\n";
} else {
    echo "10. Skipping DELETE Barang Detail (no ID available)\n\n";
}

// Test 11: Verify Deletion
if ($testDetailId) {
    echo "11. Testing GET Deleted Barang Detail (should return 404)\n";
    echo "GET {$baseUrl}/divisi/{$testKodeDivisi}/barangs/{$testKodeBarang}/details/{$testDetailId}\n";

    $response = makeRequest(
        "{$baseUrl}/divisi/{$testKodeDivisi}/barangs/{$testKodeBarang}/details/{$testDetailId}",
        'GET',
        null,
        $authToken
    );

    echo "Status: {$response['status']}\n";
    echo "Response: " . json_encode($response['body'], JSON_PRETTY_PRINT) . "\n\n";
} else {
    echo "11. Skipping GET Deleted Barang Detail (no ID available)\n\n";
}

echo "=== DBarang API Testing Complete ===\n";

// Instructions for running the test
echo "\n=== USAGE INSTRUCTIONS ===\n";
echo "1. Make sure your Laravel server is running: php artisan serve\n";
echo "2. Update the \$authToken variable with a valid Sanctum token\n";
echo "3. Update test data (\$testKodeDivisi, \$testKodeBarang) with existing values from your database\n";
echo "4. Make sure the division and product exist in your database\n";
echo "5. Run this script: php test_d_barang_api.php\n";
echo "6. Check the responses for success/error status codes and messages\n\n";

echo "Expected HTTP Status Codes:\n";
echo "- 200: Success for GET, PUT requests\n";
echo "- 201: Success for POST (create) requests\n";
echo "- 404: Resource not found\n";
echo "- 422: Validation errors\n";
echo "- 500: Server errors\n";

?>
