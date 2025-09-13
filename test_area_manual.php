<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Testing Area API Components ===\n\n";

try {
    // Test 1: Check if Area model can be loaded
    echo "1. Testing Area Model...\n";
    $area = new App\Models\Area();
    echo "   ✓ Area model loaded successfully\n";
    echo "   ✓ Table: " . $area->getTable() . "\n";
    echo "   ✓ Primary Key: " . implode(', ', $area->getKeyName()) . "\n";
    echo "   ✓ Fillable: " . implode(', ', $area->getFillable()) . "\n\n";

    // Test 2: Check if requests can be loaded
    echo "2. Testing Request Classes...\n";
    $storeRequest = new App\Http\Requests\StoreAreaRequest();
    echo "   ✓ StoreAreaRequest loaded successfully\n";
    
    $updateRequest = new App\Http\Requests\UpdateAreaRequest();
    echo "   ✓ UpdateAreaRequest loaded successfully\n\n";

    // Test 3: Check if resources can be loaded
    echo "3. Testing Resource Classes...\n";
    $resource = new App\Http\Resources\AreaResource(null);
    echo "   ✓ AreaResource loaded successfully\n";
    
    $collection = new App\Http\Resources\AreaCollection(collect());
    echo "   ✓ AreaCollection loaded successfully\n\n";

    // Test 4: Check if controller can be loaded
    echo "4. Testing Controller...\n";
    $controller = new App\Http\Controllers\AreaController();
    echo "   ✓ AreaController loaded successfully\n\n";

    // Test 5: Check relationships
    echo "5. Testing Model Relationships...\n";
    $area = new App\Models\Area([
        'kode_divisi' => 'D001',
        'kode_area' => 'A001',
        'area' => 'Test Area',
        'status' => true
    ]);
    
    echo "   ✓ Divisi relationship: " . (method_exists($area, 'divisi') ? 'Available' : 'Missing') . "\n";
    echo "   ✓ Customers relationship: " . (method_exists($area, 'customers') ? 'Available' : 'Missing') . "\n";
    echo "   ✓ Sales relationship: " . (method_exists($area, 'sales') ? 'Available' : 'Missing') . "\n\n";

    echo "🎉 All Area API components loaded successfully!\n\n";

    // Test 6: Check routes
    echo "6. Checking Route Registration...\n";
    $routes = app('router')->getRoutes();
    $areaRoutes = [];
    
    foreach ($routes as $route) {
        $uri = $route->uri();
        if (strpos($uri, 'areas') !== false) {
            $areaRoutes[] = $route->methods()[0] . ' ' . $uri;
        }
    }
    
    echo "   Registered Area Routes:\n";
    foreach ($areaRoutes as $route) {
        echo "   - $route\n";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "   File: " . $e->getFile() . "\n";
    echo "   Line: " . $e->getLine() . "\n";
}
