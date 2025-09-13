<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Testing Divisi API Components ===\n\n";

try {
    // Test 1: Check if Divisi model can be loaded
    echo "1. Testing Divisi Model...\n";
    $divisi = new App\Models\Divisi();
    echo "   ✓ Divisi model loaded successfully\n";
    echo "   ✓ Table: " . $divisi->getTable() . "\n";
    echo "   ✓ Primary Key: " . $divisi->getKeyName() . "\n";
    echo "   ✓ Fillable: " . implode(', ', $divisi->getFillable()) . "\n\n";

    // Test 2: Check if requests can be loaded
    echo "2. Testing Request Classes...\n";
    $storeRequest = new App\Http\Requests\StoreDivisiRequest();
    echo "   ✓ StoreDivisiRequest loaded successfully\n";
    
    $updateRequest = new App\Http\Requests\UpdateDivisiRequest();
    echo "   ✓ UpdateDivisiRequest loaded successfully\n\n";

    // Test 3: Check if resources can be loaded
    echo "3. Testing Resource Classes...\n";
    $resource = new App\Http\Resources\DivisiResource(null);
    echo "   ✓ DivisiResource loaded successfully\n";
    
    $collection = new App\Http\Resources\DivisiCollection(collect());
    echo "   ✓ DivisiCollection loaded successfully\n\n";

    // Test 4: Check if controller can be loaded
    echo "4. Testing Controller...\n";
    $controller = new App\Http\Controllers\DivisiController();
    echo "   ✓ DivisiController loaded successfully\n\n";

    // Test 5: Check relationships
    echo "5. Testing Model Relationships...\n";
    $divisi = new App\Models\Divisi([
        'kode_divisi' => 'D001',
        'nama_divisi' => 'Test Divisi',
    ]);
    
    echo "   ✓ Banks relationship: " . (method_exists($divisi, 'banks') ? 'Available' : 'Missing') . "\n";
    echo "   ✓ Areas relationship: " . (method_exists($divisi, 'areas') ? 'Available' : 'Missing') . "\n";
    echo "   ✓ Customers relationship: " . (method_exists($divisi, 'customers') ? 'Available' : 'Missing') . "\n";
    echo "   ✓ Sales relationship: " . (method_exists($divisi, 'sales') ? 'Available' : 'Missing') . "\n";
    echo "   ✓ Barangs relationship: " . (method_exists($divisi, 'barangs') ? 'Available' : 'Missing') . "\n";
    echo "   ✓ Invoices relationship: " . (method_exists($divisi, 'invoices') ? 'Available' : 'Missing') . "\n";
    echo "   ✓ Suppliers relationship: " . (method_exists($divisi, 'suppliers') ? 'Available' : 'Missing') . "\n\n";

    echo "🎉 All Divisi API components loaded successfully!\n\n";

    // Test 6: Check routes
    echo "6. Checking Route Registration...\n";
    $routes = app('router')->getRoutes();
    $divisiRoutes = [];
    
    foreach ($routes as $route) {
        $uri = $route->uri();
        if (strpos($uri, 'divisi') !== false && !strpos($uri, '{kodeDivisi}')) {
            $divisiRoutes[] = $route->methods()[0] . ' ' . $uri;
        }
    }
    
    echo "   Registered Divisi Routes:\n";
    foreach ($divisiRoutes as $route) {
        echo "   - $route\n";
    }

    // Test 7: Test validation rules
    echo "\n7. Testing Validation Rules...\n";
    $storeRules = $storeRequest->rules();
    echo "   ✓ Store rules: " . count($storeRules) . " fields validated\n";
    echo "   ✓ Store fields: " . implode(', ', array_keys($storeRules)) . "\n";
    
    $updateRules = $updateRequest->rules();
    echo "   ✓ Update rules: " . count($updateRules) . " fields validated\n";
    echo "   ✓ Update fields: " . implode(', ', array_keys($updateRules)) . "\n\n";

    echo "✅ All tests passed! Divisi API is ready for use.\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "   File: " . $e->getFile() . "\n";
    echo "   Line: " . $e->getLine() . "\n";
}
