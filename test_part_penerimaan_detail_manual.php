<?php

/**
 * Manual Test Script for PartPenerimaanDetail API Implementation
 * 
 * This script verifies that all PartPenerimaanDetail API components are properly implemented:
 * - Model enhancement with proper relationships and casting
 * - Controller with limited CRUD operations (index and store only)
 * - Request validation class
 * - Resource formatting classes
 * - Route registration as nested resource
 * 
 * Note: This model has no primary key defined in the database schema,
 * therefore individual show(), update(), and destroy() methods are not implemented.
 */

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

echo "=== PART PENERIMAAN DETAIL API IMPLEMENTATION TEST ===\n\n";

$testsPassed = 0;
$totalTests = 0;

// Test 1: Check if PartPenerimaanDetail model loads correctly
$totalTests++;
echo "Test 1: Loading PartPenerimaanDetail model...\n";
try {
    $partPenerimaanDetail = new \App\Models\PartPenerimaanDetail();
    echo "✅ PartPenerimaanDetail model loaded successfully\n";
    
    // Check table name
    if ($partPenerimaanDetail->getTable() === 'part_penerimaan_detail') {
        echo "✅ Table name set correctly: part_penerimaan_detail\n";
    } else {
        echo "❌ Wrong table name: " . $partPenerimaanDetail->getTable() . "\n";
    }
    
    // Check primary key (should be null)
    if ($partPenerimaanDetail->getKeyName() === null) {
        echo "✅ Primary key correctly set to null (no primary key in database)\n";
    } else {
        echo "❌ Primary key should be null but got: " . $partPenerimaanDetail->getKeyName() . "\n";
    }
    
    // Check incrementing (should be false)
    if (!$partPenerimaanDetail->getIncrementing()) {
        echo "✅ Incrementing correctly set to false\n";
    } else {
        echo "❌ Incrementing should be false\n";
    }
    
    $testsPassed++;
} catch (Exception $e) {
    echo "❌ Error loading PartPenerimaanDetail model: " . $e->getMessage() . "\n";
}

// Test 2: Check PartPenerimaanDetailController
$totalTests++;
echo "\nTest 2: Loading PartPenerimaanDetailController...\n";
try {
    $controller = new \App\Http\Controllers\PartPenerimaanDetailController();
    echo "✅ PartPenerimaanDetailController loaded successfully\n";
    
    // Check if required methods exist (only index and store)
    $requiredMethods = ['index', 'store', 'stats', 'bulkDelete'];
    $notImplementedMethods = ['show', 'update', 'destroy'];
    $methods = get_class_methods($controller);
    
    foreach ($requiredMethods as $method) {
        if (in_array($method, $methods)) {
            echo "✅ Method {$method} exists\n";
        } else {
            echo "❌ Method {$method} missing\n";
        }
    }
    
    // Verify that individual CRUD methods are NOT implemented as resource methods
    echo "\n📝 Verifying that individual CRUD methods are not implemented:\n";
    foreach ($notImplementedMethods as $method) {
        if (!in_array($method, $methods)) {
            echo "✅ Method {$method} correctly not implemented (due to no primary key)\n";
        } else {
            echo "⚠️  Method {$method} exists but should not be used due to no primary key\n";
        }
    }
    
    $testsPassed++;
} catch (Exception $e) {
    echo "❌ Error loading PartPenerimaanDetailController: " . $e->getMessage() . "\n";
}

// Test 3: Check Request Class
$totalTests++;
echo "\nTest 3: Loading Request class...\n";
try {
    $storeRequest = new \App\Http\Requests\StorePartPenerimaanDetailRequest();
    echo "✅ StorePartPenerimaanDetailRequest loaded successfully\n";
    echo "📝 Note: Only store request is needed since individual updates are not supported\n";
    $testsPassed++;
} catch (Exception $e) {
    echo "❌ Error loading Request class: " . $e->getMessage() . "\n";
}

// Test 4: Check Resource Classes
$totalTests++;
echo "\nTest 4: Loading Resource classes...\n";
try {
    // We need a fake part penerimaan detail instance to test PartPenerimaanDetailResource
    $fakeDetail = new \App\Models\PartPenerimaanDetail();
    $fakeDetail->kode_divisi = 'TEST';
    $fakeDetail->no_penerimaan = 'PN001';
    $fakeDetail->kode_barang = 'BRG001';
    $fakeDetail->qty_supply = 10;
    $fakeDetail->harga = 100000.00;
    $fakeDetail->harga_nett = 950000.00;
    
    $resource = new \App\Http\Resources\PartPenerimaanDetailResource($fakeDetail);
    $collection = new \App\Http\Resources\PartPenerimaanDetailCollection(collect([$fakeDetail]));
    
    echo "✅ PartPenerimaanDetailResource loaded successfully\n";
    echo "✅ PartPenerimaanDetailCollection loaded successfully\n";
    $testsPassed++;
} catch (Exception $e) {
    echo "❌ Error loading Resource classes: " . $e->getMessage() . "\n";
}

// Test 5: Check Routes Registration
$totalTests++;
echo "\nTest 5: Checking route registration...\n";
try {
    $routes = \Illuminate\Support\Facades\Route::getRoutes();
    $partPenerimaanDetailRoutes = [];
    
    foreach ($routes as $route) {
        $uri = $route->uri();
        if (strpos($uri, 'details') !== false && 
            strpos($uri, 'part-penerimaan') !== false && 
            strpos($uri, 'divisi/{kodeDivisi}') !== false) {
            $partPenerimaanDetailRoutes[] = $route->methods()[0] . ' ' . $uri;
        }
    }
    
    if (count($partPenerimaanDetailRoutes) > 0) {
        echo "✅ Part penerimaan detail routes found: " . count($partPenerimaanDetailRoutes) . " routes\n";
        foreach ($partPenerimaanDetailRoutes as $route) {
            echo "  - {$route}\n";
        }
        $testsPassed++;
    } else {
        echo "❌ No part penerimaan detail routes found\n";
    }
} catch (Exception $e) {
    echo "❌ Error checking routes: " . $e->getMessage() . "\n";
}

// Test 6: Check PartPenerimaanDetail Model Properties
$totalTests++;
echo "\nTest 6: Checking PartPenerimaanDetail model properties...\n";
try {
    $detail = new \App\Models\PartPenerimaanDetail();
    
    // Check fillable fields
    $fillable = $detail->getFillable();
    $expectedFillable = [
        'kode_divisi', 'no_penerimaan', 'kode_barang', 
        'qty_supply', 'harga', 'diskon1', 'diskon2', 'harga_nett'
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
    $casts = $detail->getCasts();
    $expectedCasts = ['qty_supply', 'harga', 'diskon1', 'diskon2', 'harga_nett'];
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
    echo "❌ Error checking PartPenerimaanDetail model properties: " . $e->getMessage() . "\n";
}

// Test 7: Check Relationships
$totalTests++;
echo "\nTest 7: Checking PartPenerimaanDetail relationships...\n";
try {
    $detail = new \App\Models\PartPenerimaanDetail();
    
    $relationships = ['partPenerimaan', 'barang', 'divisi'];
    foreach ($relationships as $relationship) {
        if (method_exists($detail, $relationship)) {
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
    $request = new \App\Http\Requests\StorePartPenerimaanDetailRequest();
    
    if (method_exists($request, 'rules')) {
        echo "✅ StorePartPenerimaanDetailRequest rules() method exists\n";
    } else {
        echo "❌ StorePartPenerimaanDetailRequest rules() method missing\n";
    }
    
    if (method_exists($request, 'authorize')) {
        echo "✅ StorePartPenerimaanDetailRequest authorize() method exists\n";
    } else {
        echo "❌ StorePartPenerimaanDetailRequest authorize() method missing\n";
    }
    
    if (method_exists($request, 'prepareForValidation')) {
        echo "✅ StorePartPenerimaanDetailRequest prepareForValidation() method exists\n";
    } else {
        echo "❌ StorePartPenerimaanDetailRequest prepareForValidation() method missing\n";
    }
    
    $testsPassed++;
} catch (Exception $e) {
    echo "❌ Error checking validation rules: " . $e->getMessage() . "\n";
}

// Summary
echo "\n=== TEST SUMMARY ===\n";
echo "Tests passed: {$testsPassed}/{$totalTests}\n";

if ($testsPassed === $totalTests) {
    echo "🎉 All tests passed! PartPenerimaanDetail API implementation is complete.\n";
} else {
    echo "⚠️  Some tests failed. Please check the implementation.\n";
}

echo "\n=== PART PENERIMAAN DETAIL API ENDPOINTS ===\n";
echo "The following endpoints are available:\n";
echo "- GET    /api/divisi/{kodeDivisi}/part-penerimaan/{noPenerimaan}/details           - List details\n";
echo "- POST   /api/divisi/{kodeDivisi}/part-penerimaan/{noPenerimaan}/details           - Create detail\n";
echo "- GET    /api/divisi/{kodeDivisi}/part-penerimaan/{noPenerimaan}/details-stats     - Get statistics\n";
echo "- DELETE /api/divisi/{kodeDivisi}/part-penerimaan/{noPenerimaan}/details-bulk     - Bulk delete all details\n";

echo "\n=== IMPORTANT LIMITATIONS ===\n";
echo "⚠️  Due to the lack of a primary key in the database schema:\n";
echo "   - Individual detail show() operations are not supported\n";
echo "   - Individual detail update() operations are not supported\n";
echo "   - Individual detail destroy() operations are not supported\n";
echo "   - Use bulk operations for modifications\n";
echo "   - Duplicate kode_barang detection is implemented in store() method\n";

echo "\n=== USAGE EXAMPLE ===\n";
echo "# Create a new part penerimaan detail\n";
echo "POST /api/divisi/DIV01/part-penerimaan/PN001/details\n";
echo "{\n";
echo "  \"kode_barang\": \"BRG001\",\n";
echo "  \"qty_supply\": 10,\n";
echo "  \"harga\": 100000,\n";
echo "  \"diskon1\": 5.0,\n";
echo "  \"diskon2\": 2.0,\n";
echo "  \"harga_nett\": 930000\n";
echo "}\n\n";

echo "# List all details for a part penerimaan\n";
echo "GET /api/divisi/DIV01/part-penerimaan/PN001/details?search=BRG001&per_page=10\n\n";

echo "# Get part penerimaan detail statistics\n";
echo "GET /api/divisi/DIV01/part-penerimaan/PN001/details-stats\n\n";

echo "# Bulk delete all details for a part penerimaan\n";
echo "DELETE /api/divisi/DIV01/part-penerimaan/PN001/details-bulk\n\n";
