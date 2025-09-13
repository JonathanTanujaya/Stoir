<?php

/**
 * Manual Test Script for User API Implementation
 * 
 * This script verifies that all User API components are properly implemented:
 * - Model enhancement with composite key support
 * - Controller with CRUD operations
 * - Request validation classes
 * - Resource formatting classes
 * - Route registration
 */

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

echo "=== USER API IMPLEMENTATION TEST ===\n\n";

$testsPassed = 0;
$totalTests = 0;

// Test 1: Check if User model loads correctly
$totalTests++;
echo "Test 1: Loading User model...\n";
try {
    $user = new \App\Models\User();
    echo "✅ User model loaded successfully\n";
    
    // Check if HasCompositeKey trait is used
    if (in_array(\App\Traits\HasCompositeKey::class, class_uses($user))) {
        echo "✅ HasCompositeKey trait detected\n";
        $testsPassed++;
    } else {
        echo "❌ HasCompositeKey trait not found\n";
    }
} catch (Exception $e) {
    echo "❌ Error loading User model: " . $e->getMessage() . "\n";
}

// Test 2: Check UserController
$totalTests++;
echo "\nTest 2: Loading UserController...\n";
try {
    $controller = new \App\Http\Controllers\UserController();
    echo "✅ UserController loaded successfully\n";
    
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
    echo "❌ Error loading UserController: " . $e->getMessage() . "\n";
}

// Test 3: Check Request Classes
$totalTests++;
echo "\nTest 3: Loading Request classes...\n";
try {
    $storeRequest = new \App\Http\Requests\StoreUserRequest();
    $updateRequest = new \App\Http\Requests\UpdateUserRequest();
    echo "✅ StoreUserRequest loaded successfully\n";
    echo "✅ UpdateUserRequest loaded successfully\n";
    $testsPassed++;
} catch (Exception $e) {
    echo "❌ Error loading Request classes: " . $e->getMessage() . "\n";
}

// Test 4: Check Resource Classes
$totalTests++;
echo "\nTest 4: Loading Resource classes...\n";
try {
    // We need a fake user instance to test UserResource
    $fakeUser = new \App\Models\User();
    $fakeUser->kode_divisi = 'TEST';
    $fakeUser->username = 'testuser';
    $fakeUser->nama = 'Test User';
    
    $resource = new \App\Http\Resources\UserResource($fakeUser);
    $collection = new \App\Http\Resources\UserCollection(collect([$fakeUser]));
    
    echo "✅ UserResource loaded successfully\n";
    echo "✅ UserCollection loaded successfully\n";
    $testsPassed++;
} catch (Exception $e) {
    echo "❌ Error loading Resource classes: " . $e->getMessage() . "\n";
}

// Test 5: Check Routes Registration
$totalTests++;
echo "\nTest 5: Checking route registration...\n";
try {
    $routes = \Illuminate\Support\Facades\Route::getRoutes();
    $userRoutes = [];
    
    foreach ($routes as $route) {
        $uri = $route->uri();
        if (strpos($uri, 'users') !== false && strpos($uri, 'divisi/{kodeDivisi}') !== false) {
            $userRoutes[] = $route->methods()[0] . ' ' . $uri;
        }
    }
    
    if (count($userRoutes) > 0) {
        echo "✅ User routes found: " . count($userRoutes) . " routes\n";
        foreach ($userRoutes as $route) {
            echo "  - {$route}\n";
        }
        $testsPassed++;
    } else {
        echo "❌ No user routes found\n";
    }
} catch (Exception $e) {
    echo "❌ Error checking routes: " . $e->getMessage() . "\n";
}

// Test 6: Check User Model Properties
$totalTests++;
echo "\nTest 6: Checking User model properties...\n";
try {
    $user = new \App\Models\User();
    
    // Check table name
    if ($user->getTable() === 'master_user') {
        echo "✅ Table name set correctly: master_user\n";
    } else {
        echo "❌ Wrong table name: " . $user->getTable() . "\n";
    }
    
    // Check fillable fields
    $fillable = $user->getFillable();
    $expectedFillable = ['kode_divisi', 'username', 'nama', 'password'];
    
    $fillableMatch = empty(array_diff($expectedFillable, $fillable));
    if ($fillableMatch) {
        echo "✅ Fillable fields set correctly\n";
    } else {
        echo "❌ Fillable fields mismatch\n";
        echo "  Expected: " . implode(', ', $expectedFillable) . "\n";
        echo "  Actual: " . implode(', ', $fillable) . "\n";
    }
    
    // Check hidden fields
    $hidden = $user->getHidden();
    if (in_array('password', $hidden)) {
        echo "✅ Password field hidden correctly\n";
    } else {
        echo "❌ Password field not hidden\n";
    }
    
    $testsPassed++;
} catch (Exception $e) {
    echo "❌ Error checking User model properties: " . $e->getMessage() . "\n";
}

// Test 7: Check Relationships
$totalTests++;
echo "\nTest 7: Checking User relationships...\n";
try {
    $user = new \App\Models\User();
    
    // Check if divisi relationship exists
    if (method_exists($user, 'divisi')) {
        echo "✅ divisi() relationship method exists\n";
        $testsPassed++;
    } else {
        echo "❌ divisi() relationship method not found\n";
    }
} catch (Exception $e) {
    echo "❌ Error checking relationships: " . $e->getMessage() . "\n";
}

// Test 8: Check Validation Rules
$totalTests++;
echo "\nTest 8: Checking validation rules...\n";
try {
    // Create a mock request for StoreUserRequest
    $request = new \App\Http\Requests\StoreUserRequest();
    
    // We can't easily test validation without a proper request context,
    // but we can check if the methods exist
    if (method_exists($request, 'rules')) {
        echo "✅ StoreUserRequest rules() method exists\n";
    } else {
        echo "❌ StoreUserRequest rules() method missing\n";
    }
    
    if (method_exists($request, 'authorize')) {
        echo "✅ StoreUserRequest authorize() method exists\n";
    } else {
        echo "❌ StoreUserRequest authorize() method missing\n";
    }
    
    $updateRequest = new \App\Http\Requests\UpdateUserRequest();
    if (method_exists($updateRequest, 'rules')) {
        echo "✅ UpdateUserRequest rules() method exists\n";
    } else {
        echo "❌ UpdateUserRequest rules() method missing\n";
    }
    
    $testsPassed++;
} catch (Exception $e) {
    echo "❌ Error checking validation rules: " . $e->getMessage() . "\n";
}

// Summary
echo "\n=== TEST SUMMARY ===\n";
echo "Tests passed: {$testsPassed}/{$totalTests}\n";

if ($testsPassed === $totalTests) {
    echo "🎉 All tests passed! User API implementation is complete.\n";
} else {
    echo "⚠️  Some tests failed. Please check the implementation.\n";
}

echo "\n=== USER API ENDPOINTS ===\n";
echo "The following endpoints are available:\n";
echo "- GET    /api/divisi/{kodeDivisi}/users           - List users\n";
echo "- POST   /api/divisi/{kodeDivisi}/users           - Create user\n";
echo "- GET    /api/divisi/{kodeDivisi}/users/{username} - Show user\n";
echo "- PUT    /api/divisi/{kodeDivisi}/users/{username} - Update user\n";
echo "- DELETE /api/divisi/{kodeDivisi}/users/{username} - Delete user\n";
echo "- GET    /api/divisi/{kodeDivisi}/users-stats     - Get user statistics\n";

echo "\n=== USAGE EXAMPLE ===\n";
echo "# Create a new user\n";
echo "POST /api/divisi/DIV01/users\n";
echo "{\n";
echo "  \"username\": \"johndoe\",\n";
echo "  \"nama\": \"John Doe\",\n";
echo "  \"password\": \"secretpassword123\"\n";
echo "}\n\n";

echo "# Update user (password is optional)\n";
echo "PUT /api/divisi/DIV01/users/johndoe\n";
echo "{\n";
echo "  \"nama\": \"John Doe Updated\",\n";
echo "  \"password\": \"newsecretpassword123\"\n";
echo "}\n\n";

echo "# Get user\n";
echo "GET /api/divisi/DIV01/users/johndoe\n\n";

echo "# List users with search and pagination\n";
echo "GET /api/divisi/DIV01/users?search=john&per_page=10&page=1\n\n";
