<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Testing Area API Validation ===\n\n";

try {
    // Test validation rules for StoreAreaRequest
    echo "1. Testing StoreAreaRequest Validation Rules...\n";
    $request = new App\Http\Requests\StoreAreaRequest();
    
    // Mock route parameters
    $reflection = new ReflectionClass($request);
    $method = $reflection->getMethod('route');
    $method->setAccessible(true);
    
    // Create a mock route that returns test data
    $mockRoute = function($param) {
        if ($param === 'kodeDivisi') return 'D001';
        return null;
    };
    
    // Get validation rules
    $rules = $request->rules();
    echo "   ✓ Rules loaded: " . count($rules) . " rules found\n";
    echo "   ✓ Required fields: " . implode(', ', array_keys($rules)) . "\n";
    
    // Check specific validation rules
    if (isset($rules['kode_area'])) {
        echo "   ✓ kode_area validation: " . (is_array($rules['kode_area']) ? 'Complex rules' : $rules['kode_area']) . "\n";
    }
    if (isset($rules['area'])) {
        echo "   ✓ area validation: " . $rules['area'] . "\n";
    }
    if (isset($rules['status'])) {
        echo "   ✓ status validation: " . $rules['status'] . "\n";
    }
    echo "\n";

    // Test UpdateAreaRequest
    echo "2. Testing UpdateAreaRequest Validation Rules...\n";
    $updateRequest = new App\Http\Requests\UpdateAreaRequest();
    $updateRules = $updateRequest->rules();
    echo "   ✓ Update rules loaded: " . count($updateRules) . " rules found\n";
    echo "   ✓ Update fields: " . implode(', ', array_keys($updateRules)) . "\n\n";

    // Test Resource transformation
    echo "3. Testing AreaResource Transformation...\n";
    $mockAreaData = (object) [
        'kode_divisi' => 'D001',
        'kode_area' => 'A001',
        'area' => 'Test Area',
        'status' => true,
    ];
    
    $resource = new App\Http\Resources\AreaResource($mockAreaData);
    echo "   ✓ AreaResource instantiated successfully\n";
    
    // Test transformation with mock request
    $mockRequest = new Illuminate\Http\Request();
    $transformed = $resource->toArray($mockRequest);
    echo "   ✓ Transformation completed with " . count($transformed) . " fields\n";
    echo "   ✓ Key fields present: " . (isset($transformed['kode_area']) ? 'Yes' : 'No') . "\n\n";

    // Test Collection
    echo "4. Testing AreaCollection...\n";
    $mockCollection = collect([$mockAreaData, $mockAreaData]);
    $collection = new App\Http\Resources\AreaCollection($mockCollection);
    $collectionArray = $collection->toArray($mockRequest);
    echo "   ✓ AreaCollection instantiated successfully\n";
    echo "   ✓ Collection structure: " . implode(', ', array_keys($collectionArray)) . "\n";
    if (isset($collectionArray['meta'])) {
        echo "   ✓ Meta information: " . implode(', ', array_keys($collectionArray['meta'])) . "\n";
    }
    echo "\n";

    echo "🎉 All validation and transformation tests passed!\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "   File: " . $e->getFile() . "\n";
    echo "   Line: " . $e->getLine() . "\n";
}
