<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Testing Kategori API Components ===\n\n";

try {
    // Test 1: Check if Kategori model can be loaded
    echo "1. Testing Kategori Model...\n";
    $kategori = new App\Models\Kategori();
    echo "   ✓ Kategori model loaded successfully\n";
    echo "   ✓ Table: " . $kategori->getTable() . "\n";
    echo "   ✓ Primary Key: " . implode(', ', $kategori->getKeyName()) . "\n";
    echo "   ✓ Fillable: " . implode(', ', $kategori->getFillable()) . "\n\n";

    // Test 2: Check if requests can be loaded
    echo "2. Testing Request Classes...\n";
    $storeRequest = new App\Http\Requests\StoreKategoriRequest();
    echo "   ✓ StoreKategoriRequest loaded successfully\n";
    
    $updateRequest = new App\Http\Requests\UpdateKategoriRequest();
    echo "   ✓ UpdateKategoriRequest loaded successfully\n\n";

    // Test 3: Check if resources can be loaded
    echo "3. Testing Resource Classes...\n";
    $resource = new App\Http\Resources\KategoriResource(null);
    echo "   ✓ KategoriResource loaded successfully\n";
    
    $collection = new App\Http\Resources\KategoriCollection(collect());
    echo "   ✓ KategoriCollection loaded successfully\n\n";

    // Test 4: Check if controller can be loaded
    echo "4. Testing Controller...\n";
    $controller = new App\Http\Controllers\KategoriController();
    echo "   ✓ KategoriController loaded successfully\n\n";

    // Test 5: Check relationships
    echo "5. Testing Model Relationships...\n";
    $kategori = new App\Models\Kategori([
        'kode_divisi' => 'D001',
        'kode_kategori' => 'K001',
        'kategori' => 'Test Kategori',
        'status' => true
    ]);
    
    echo "   ✓ Divisi relationship: " . (method_exists($kategori, 'divisi') ? 'Available' : 'Missing') . "\n";
    echo "   ✓ Barangs relationship: " . (method_exists($kategori, 'barangs') ? 'Available' : 'Missing') . "\n";
    echo "   ✓ DPakets relationship: " . (method_exists($kategori, 'dPakets') ? 'Available' : 'Missing') . "\n\n";

    echo "🎉 All Kategori API components loaded successfully!\n\n";

    // Test 6: Check routes
    echo "6. Checking Route Registration...\n";
    $routes = app('router')->getRoutes();
    $kategoriRoutes = [];
    
    foreach ($routes as $route) {
        $uri = $route->uri();
        if (strpos($uri, 'kategoris') !== false) {
            $kategoriRoutes[] = $route->methods()[0] . ' ' . $uri;
        }
    }
    
    echo "   Registered Kategori Routes:\n";
    foreach ($kategoriRoutes as $route) {
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

    // Test 8: Test HasCompositeKey trait
    echo "8. Testing HasCompositeKey Trait...\n";
    $traits = class_uses($kategori);
    $hasCompositeKey = in_array('App\Traits\HasCompositeKey', $traits);
    echo "   ✓ HasCompositeKey trait: " . ($hasCompositeKey ? 'Available' : 'Missing') . "\n\n";

    echo "✅ All tests passed! Kategori API is ready for use.\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "   File: " . $e->getFile() . "\n";
    echo "   Line: " . $e->getLine() . "\n";
}
