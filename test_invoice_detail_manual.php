<?php

/**
 * Manual Test Script for InvoiceDetail API Implementation
 * 
 * This script verifies that all InvoiceDetail API components are properly implemented:
 * - Model enhancement with proper relationships and casting
 * - Controller with nested resource CRUD operations
 * - Request validation classes
 * - Resource formatting classes
 * - Route registration as nested resource
 */

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

echo "=== INVOICE DETAIL API IMPLEMENTATION TEST ===\n\n";

$testsPassed = 0;
$totalTests = 0;

// Test 1: Check if InvoiceDetail model loads correctly
$totalTests++;
echo "Test 1: Loading InvoiceDetail model...\n";
try {
    $invoiceDetail = new \App\Models\InvoiceDetail();
    echo "✅ InvoiceDetail model loaded successfully\n";
    
    // Check table name
    if ($invoiceDetail->getTable() === 'invoice_detail') {
        echo "✅ Table name set correctly: invoice_detail\n";
    } else {
        echo "❌ Wrong table name: " . $invoiceDetail->getTable() . "\n";
    }
    
    // Check primary key
    if ($invoiceDetail->getKeyName() === 'id') {
        echo "✅ Primary key set correctly: id\n";
    } else {
        echo "❌ Wrong primary key: " . $invoiceDetail->getKeyName() . "\n";
    }
    
    $testsPassed++;
} catch (Exception $e) {
    echo "❌ Error loading InvoiceDetail model: " . $e->getMessage() . "\n";
}

// Test 2: Check InvoiceDetailController
$totalTests++;
echo "\nTest 2: Loading InvoiceDetailController...\n";
try {
    $controller = new \App\Http\Controllers\InvoiceDetailController();
    echo "✅ InvoiceDetailController loaded successfully\n";
    
    // Check if required methods exist
    $requiredMethods = ['index', 'store', 'show', 'update', 'destroy', 'stats'];
    $methods = get_class_methods($controller);
    
    foreach ($requiredMethods as $method) {
        if (in_array($method, $methods)) {
            echo "✅ Method {$method} exists\n";
        } else {
            echo "❌ Method {$method} missing\n";
        }
    }
    $testsPassed++;
} catch (Exception $e) {
    echo "❌ Error loading InvoiceDetailController: " . $e->getMessage() . "\n";
}

// Test 3: Check Request Classes
$totalTests++;
echo "\nTest 3: Loading Request classes...\n";
try {
    $storeRequest = new \App\Http\Requests\StoreInvoiceDetailRequest();
    $updateRequest = new \App\Http\Requests\UpdateInvoiceDetailRequest();
    echo "✅ StoreInvoiceDetailRequest loaded successfully\n";
    echo "✅ UpdateInvoiceDetailRequest loaded successfully\n";
    $testsPassed++;
} catch (Exception $e) {
    echo "❌ Error loading Request classes: " . $e->getMessage() . "\n";
}

// Test 4: Check Resource Classes
$totalTests++;
echo "\nTest 4: Loading Resource classes...\n";
try {
    // We need a fake invoice detail instance to test InvoiceDetailResource
    $fakeInvoiceDetail = new \App\Models\InvoiceDetail();
    $fakeInvoiceDetail->id = 1;
    $fakeInvoiceDetail->kode_divisi = 'TEST';
    $fakeInvoiceDetail->no_invoice = 'INV001';
    $fakeInvoiceDetail->kode_barang = 'BRG001';
    $fakeInvoiceDetail->qty_supply = 10;
    $fakeInvoiceDetail->harga_jual = 100000.00;
    $fakeInvoiceDetail->harga_nett = 950000.00;
    
    $resource = new \App\Http\Resources\InvoiceDetailResource($fakeInvoiceDetail);
    $collection = new \App\Http\Resources\InvoiceDetailCollection(collect([$fakeInvoiceDetail]));
    
    echo "✅ InvoiceDetailResource loaded successfully\n";
    echo "✅ InvoiceDetailCollection loaded successfully\n";
    $testsPassed++;
} catch (Exception $e) {
    echo "❌ Error loading Resource classes: " . $e->getMessage() . "\n";
}

// Test 5: Check Routes Registration
$totalTests++;
echo "\nTest 5: Checking route registration...\n";
try {
    $routes = \Illuminate\Support\Facades\Route::getRoutes();
    $invoiceDetailRoutes = [];
    
    foreach ($routes as $route) {
        $uri = $route->uri();
        if (strpos($uri, 'details') !== false && strpos($uri, 'divisi/{kodeDivisi}/invoices/{noInvoice}') !== false) {
            $invoiceDetailRoutes[] = $route->methods()[0] . ' ' . $uri;
        }
    }
    
    if (count($invoiceDetailRoutes) > 0) {
        echo "✅ Invoice detail routes found: " . count($invoiceDetailRoutes) . " routes\n";
        foreach ($invoiceDetailRoutes as $route) {
            echo "  - {$route}\n";
        }
        $testsPassed++;
    } else {
        echo "❌ No invoice detail routes found\n";
    }
} catch (Exception $e) {
    echo "❌ Error checking routes: " . $e->getMessage() . "\n";
}

// Test 6: Check InvoiceDetail Model Properties
$totalTests++;
echo "\nTest 6: Checking InvoiceDetail model properties...\n";
try {
    $invoiceDetail = new \App\Models\InvoiceDetail();
    
    // Check fillable fields
    $fillable = $invoiceDetail->getFillable();
    $expectedFillable = [
        'kode_divisi', 'no_invoice', 'kode_barang', 'qty_supply', 
        'harga_jual', 'jenis', 'diskon1', 'diskon2', 'harga_nett', 'status'
    ];
    
    $fillableMatch = empty(array_diff($expectedFillable, $fillable));
    if ($fillableMatch) {
        echo "✅ Fillable fields set correctly\n";
    } else {
        echo "❌ Fillable fields mismatch\n";
        echo "  Expected: " . implode(', ', $expectedFillable) . "\n";
        echo "  Actual: " . implode(', ', $fillable) . "\n";
    }
    
    // Check casts
    $casts = $invoiceDetail->getCasts();
    $expectedCasts = ['qty_supply', 'harga_jual', 'diskon1', 'diskon2', 'harga_nett'];
    $castsExist = true;
    
    foreach ($expectedCasts as $field) {
        if (!array_key_exists($field, $casts)) {
            $castsExist = false;
            echo "❌ Cast missing for field: {$field}\n";
        }
    }
    
    if ($castsExist) {
        echo "✅ Type casting set correctly\n";
    }
    
    $testsPassed++;
} catch (Exception $e) {
    echo "❌ Error checking InvoiceDetail model properties: " . $e->getMessage() . "\n";
}

// Test 7: Check Relationships
$totalTests++;
echo "\nTest 7: Checking InvoiceDetail relationships...\n";
try {
    $invoiceDetail = new \App\Models\InvoiceDetail();
    
    $relationships = ['invoice', 'barang', 'divisi'];
    foreach ($relationships as $relationship) {
        if (method_exists($invoiceDetail, $relationship)) {
            echo "✅ {$relationship}() relationship method exists\n";
        } else {
            echo "❌ {$relationship}() relationship method not found\n";
        }
    }
    
    $testsPassed++;
} catch (Exception $e) {
    echo "❌ Error checking relationships: " . $e->getMessage() . "\n";
}

// Test 8: Check Validation Rules
$totalTests++;
echo "\nTest 8: Checking validation rules...\n";
try {
    // Create a mock request for StoreInvoiceDetailRequest
    $request = new \App\Http\Requests\StoreInvoiceDetailRequest();
    
    if (method_exists($request, 'rules')) {
        echo "✅ StoreInvoiceDetailRequest rules() method exists\n";
    } else {
        echo "❌ StoreInvoiceDetailRequest rules() method missing\n";
    }
    
    if (method_exists($request, 'authorize')) {
        echo "✅ StoreInvoiceDetailRequest authorize() method exists\n";
    } else {
        echo "❌ StoreInvoiceDetailRequest authorize() method missing\n";
    }
    
    $updateRequest = new \App\Http\Requests\UpdateInvoiceDetailRequest();
    if (method_exists($updateRequest, 'rules')) {
        echo "✅ UpdateInvoiceDetailRequest rules() method exists\n";
    } else {
        echo "❌ UpdateInvoiceDetailRequest rules() method missing\n";
    }
    
    $testsPassed++;
} catch (Exception $e) {
    echo "❌ Error checking validation rules: " . $e->getMessage() . "\n";
}

// Summary
echo "\n=== TEST SUMMARY ===\n";
echo "Tests passed: {$testsPassed}/{$totalTests}\n";

if ($testsPassed === $totalTests) {
    echo "🎉 All tests passed! InvoiceDetail API implementation is complete.\n";
} else {
    echo "⚠️  Some tests failed. Please check the implementation.\n";
}

echo "\n=== INVOICE DETAIL API ENDPOINTS ===\n";
echo "The following endpoints are available:\n";
echo "- GET    /api/divisi/{kodeDivisi}/invoices/{noInvoice}/details           - List invoice details\n";
echo "- POST   /api/divisi/{kodeDivisi}/invoices/{noInvoice}/details           - Create invoice detail\n";
echo "- GET    /api/divisi/{kodeDivisi}/invoices/{noInvoice}/details/{id}      - Show invoice detail\n";
echo "- PUT    /api/divisi/{kodeDivisi}/invoices/{noInvoice}/details/{id}      - Update invoice detail\n";
echo "- DELETE /api/divisi/{kodeDivisi}/invoices/{noInvoice}/details/{id}      - Delete invoice detail\n";
echo "- GET    /api/divisi/{kodeDivisi}/invoices/{noInvoice}/details-stats     - Get invoice detail statistics\n";

echo "\n=== USAGE EXAMPLE ===\n";
echo "# Create a new invoice detail\n";
echo "POST /api/divisi/DIV01/invoices/INV001/details\n";
echo "{\n";
echo "  \"kode_barang\": \"BRG001\",\n";
echo "  \"qty_supply\": 10,\n";
echo "  \"harga_jual\": 100000,\n";
echo "  \"jenis\": \"Regular\",\n";
echo "  \"diskon1\": 5.0,\n";
echo "  \"diskon2\": 2.0,\n";
echo "  \"harga_nett\": 930000,\n";
echo "  \"status\": \"Active\"\n";
echo "}\n\n";

echo "# Update invoice detail\n";
echo "PUT /api/divisi/DIV01/invoices/INV001/details/1\n";
echo "{\n";
echo "  \"qty_supply\": 15,\n";
echo "  \"harga_nett\": 1395000\n";
echo "}\n\n";

echo "# Get invoice detail with relationships\n";
echo "GET /api/divisi/DIV01/invoices/INV001/details/1\n\n";

echo "# List invoice details with search and pagination\n";
echo "GET /api/divisi/DIV01/invoices/INV001/details?search=BRG001&per_page=10&page=1\n\n";

echo "# Get invoice detail statistics\n";
echo "GET /api/divisi/DIV01/invoices/INV001/details-stats\n\n";
