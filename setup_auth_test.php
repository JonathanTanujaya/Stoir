<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\MasterUser;
use App\Models\MDivisi;

echo "Setting up authentication test data...\n\n";

try {
    // Create test division if not exists
    $divisi = MDivisi::firstOrCreate(
        ['kodedivisi' => '01'],
        ['namadivisi' => 'Test Division', 'status' => 'Active']
    );
    echo "✅ Division created/found: {$divisi->kodedivisi} - {$divisi->namadivisi}\n";

    // Create test admin user
    $adminUser = MasterUser::where('kodedivisi', '01')
                          ->where('username', 'admin')
                          ->first();
    
    if (!$adminUser) {
        $adminUser = MasterUser::create([
            'kodedivisi' => '01',
            'username' => 'admin',
            'nama' => 'System Administrator',
            'password' => 'admin123' // Will be hashed by mutator
        ]);
        echo "✅ Admin user created: admin/admin123\n";
    } else {
        echo "✅ Admin user already exists\n";
    }

    // Create test regular user
    $regularUser = MasterUser::where('kodedivisi', '01')
                            ->where('username', 'user1')
                            ->first();
    
    if (!$regularUser) {
        $regularUser = MasterUser::create([
            'kodedivisi' => '01',
            'username' => 'user1',
            'nama' => 'Test User One',
            'password' => 'user123'
        ]);
        echo "✅ Regular user created: user1/user123\n";
    } else {
        echo "✅ Regular user already exists\n";
    }

    echo "\n=== Authentication Test Data Ready ===\n";
    echo "Admin Login: kodedivisi=01, username=admin, password=admin123\n";
    echo "User Login: kodedivisi=01, username=user1, password=user123\n";
    echo "\n=== Test Endpoints ===\n";
    echo "POST /api/auth/login - Login\n";
    echo "GET /api/auth/me - Get current user (requires auth)\n";
    echo "POST /api/auth/logout - Logout (requires auth)\n";
    echo "POST /api/auth/register - Register new user (admin only)\n";
    echo "POST /api/auth/change-password - Change password (requires auth)\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\nDone.\n";
