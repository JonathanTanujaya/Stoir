<?php

require_once 'vendor/autoload.php';

/**
 * Testing script for DBank (DetailBank) API endpoints
 * 
 * This script tests the CRUD operations for bank account management API
 * 
 * Endpoints:
 * - GET /api/divisi/{kodeDivisi}/bank-accounts (list bank accounts)
 * - POST /api/divisi/{kodeDivisi}/bank-accounts (create bank account)
 * - GET /api/divisi/{kodeDivisi}/bank-accounts/{noRekening} (show bank account)
 * - PUT /api/divisi/{kodeDivisi}/bank-accounts/{noRekening} (update bank account)
 * - DELETE /api/divisi/{kodeDivisi}/bank-accounts/{noRekening} (delete bank account)
 * - GET /api/divisi/{kodeDivisi}/bank-accounts-statistics (bank account statistics)
 */

// API Base Configuration
$baseUrl = 'http://localhost:8000/api';
$authToken = 'your-auth-token-here'; // Replace with actual Sanctum token

// Test data
$testKodeDivisi = 'DIV01';
$testNoRekening = '123456789';
$testKodeBank = 'BNI';

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

echo "=== TESTING DBANK (DetailBank) API ===\n\n";

// Test 1: Create Bank Account
echo "1. Testing CREATE Bank Account\n";
echo "POST {$baseUrl}/divisi/{$testKodeDivisi}/bank-accounts\n";

$createData = [
    'no_rekening' => $testNoRekening,
    'kode_bank' => $testKodeBank,
    'atas_nama' => 'PT Test Company',
    'saldo' => 1000000.50,
    'status' => 'AKTIF'
];

$response = makeRequest(
    "{$baseUrl}/divisi/{$testKodeDivisi}/bank-accounts", 
    'POST', 
    $createData,
    $authToken
);

echo "Status: {$response['status']}\n";
echo "Response: " . json_encode($response['body'], JSON_PRETTY_PRINT) . "\n\n";

// Test 2: Get Bank Accounts List
echo "2. Testing GET Bank Accounts List\n";
echo "GET {$baseUrl}/divisi/{$testKodeDivisi}/bank-accounts\n";

$response = makeRequest(
    "{$baseUrl}/divisi/{$testKodeDivisi}/bank-accounts",
    'GET',
    null,
    $authToken
);

echo "Status: {$response['status']}\n";
echo "Response: " . json_encode($response['body'], JSON_PRETTY_PRINT) . "\n\n";

// Test 3: Get Bank Accounts List with Filters
echo "3. Testing GET Bank Accounts List with Filters\n";
echo "GET {$baseUrl}/divisi/{$testKodeDivisi}/bank-accounts?kode_bank={$testKodeBank}&search=Test\n";

$response = makeRequest(
    "{$baseUrl}/divisi/{$testKodeDivisi}/bank-accounts?kode_bank={$testKodeBank}&search=Test",
    'GET',
    null,
    $authToken
);

echo "Status: {$response['status']}\n";
echo "Response: " . json_encode($response['body'], JSON_PRETTY_PRINT) . "\n\n";

// Test 4: Get Specific Bank Account
echo "4. Testing GET Specific Bank Account\n";
echo "GET {$baseUrl}/divisi/{$testKodeDivisi}/bank-accounts/{$testNoRekening}\n";

$response = makeRequest(
    "{$baseUrl}/divisi/{$testKodeDivisi}/bank-accounts/{$testNoRekening}",
    'GET',
    null,
    $authToken
);

echo "Status: {$response['status']}\n";
echo "Response: " . json_encode($response['body'], JSON_PRETTY_PRINT) . "\n\n";

// Test 5: Update Bank Account
echo "5. Testing UPDATE Bank Account\n";
echo "PUT {$baseUrl}/divisi/{$testKodeDivisi}/bank-accounts/{$testNoRekening}\n";

$updateData = [
    'atas_nama' => 'PT Test Company Updated',
    'saldo' => 1500000.75,
    'status' => 'AKTIF'
];

$response = makeRequest(
    "{$baseUrl}/divisi/{$testKodeDivisi}/bank-accounts/{$testNoRekening}",
    'PUT',
    $updateData,
    $authToken
);

echo "Status: {$response['status']}\n";
echo "Response: " . json_encode($response['body'], JSON_PRETTY_PRINT) . "\n\n";

// Test 6: Get Bank Account Statistics
echo "6. Testing GET Bank Account Statistics\n";
echo "GET {$baseUrl}/divisi/{$testKodeDivisi}/bank-accounts-statistics\n";

$response = makeRequest(
    "{$baseUrl}/divisi/{$testKodeDivisi}/bank-accounts-statistics",
    'GET',
    null,
    $authToken
);

echo "Status: {$response['status']}\n";
echo "Response: " . json_encode($response['body'], JSON_PRETTY_PRINT) . "\n\n";

// Test 7: Delete Bank Account
echo "7. Testing DELETE Bank Account\n";
echo "DELETE {$baseUrl}/divisi/{$testKodeDivisi}/bank-accounts/{$testNoRekening}\n";

$response = makeRequest(
    "{$baseUrl}/divisi/{$testKodeDivisi}/bank-accounts/{$testNoRekening}",
    'DELETE',
    null,
    $authToken
);

echo "Status: {$response['status']}\n";
echo "Response: " . json_encode($response['body'], JSON_PRETTY_PRINT) . "\n\n";

// Test 8: Verify Deletion
echo "8. Testing GET Deleted Bank Account (should return 404)\n";
echo "GET {$baseUrl}/divisi/{$testKodeDivisi}/bank-accounts/{$testNoRekening}\n";

$response = makeRequest(
    "{$baseUrl}/divisi/{$testKodeDivisi}/bank-accounts/{$testNoRekening}",
    'GET',
    null,
    $authToken
);

echo "Status: {$response['status']}\n";
echo "Response: " . json_encode($response['body'], JSON_PRETTY_PRINT) . "\n\n";

// Test 9: Create Bank Account with Invalid Data (Validation Test)
echo "9. Testing CREATE Bank Account with Invalid Data\n";
echo "POST {$baseUrl}/divisi/{$testKodeDivisi}/bank-accounts\n";

$invalidData = [
    'no_rekening' => '', // Empty required field
    'atas_nama' => '', // Empty required field
    'saldo' => -1000, // Negative saldo
];

$response = makeRequest(
    "{$baseUrl}/divisi/{$testKodeDivisi}/bank-accounts",
    'POST',
    $invalidData,
    $authToken
);

echo "Status: {$response['status']}\n";
echo "Response: " . json_encode($response['body'], JSON_PRETTY_PRINT) . "\n\n";

// Test 10: Get Bank Accounts for Non-existent Division
echo "10. Testing GET Bank Accounts for Non-existent Division\n";
echo "GET {$baseUrl}/divisi/INVALID/bank-accounts\n";

$response = makeRequest(
    "{$baseUrl}/divisi/INVALID/bank-accounts",
    'GET',
    null,
    $authToken
);

echo "Status: {$response['status']}\n";
echo "Response: " . json_encode($response['body'], JSON_PRETTY_PRINT) . "\n\n";

echo "=== DBank API Testing Complete ===\n";

// Instructions for running the test
echo "\n=== USAGE INSTRUCTIONS ===\n";
echo "1. Make sure your Laravel server is running: php artisan serve\n";
echo "2. Update the \$authToken variable with a valid Sanctum token\n";
echo "3. Update test data (\$testKodeDivisi, \$testKodeBank) with existing values from your database\n";
echo "4. Run this script: php test_d_bank_api.php\n";
echo "5. Check the responses for success/error status codes and messages\n\n";

echo "Expected HTTP Status Codes:\n";
echo "- 200: Success for GET, PUT requests\n";
echo "- 201: Success for POST (create) requests\n";
echo "- 404: Resource not found\n";
echo "- 422: Validation errors\n";
echo "- 500: Server errors\n";

?>
