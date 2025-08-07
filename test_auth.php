<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->detectEnvironment(function() { return 'local'; });
$app->boot();

// Test find user
$user = App\Models\MasterUser::where('kodedivisi', '01')->where('username', 'admin')->first();

if ($user) {
    echo "User found: " . $user->nama . PHP_EOL;
    echo "Password check: " . ($user->verifyPassword('admin123') ? 'OK' : 'FAIL') . PHP_EOL;
    
    try {
        $token = $user->createToken('test')->plainTextToken;
        echo "Token created: " . substr($token, 0, 20) . "..." . PHP_EOL;
    } catch (Exception $e) {
        echo "Token error: " . $e->getMessage() . PHP_EOL;
    }
} else {
    echo "User not found" . PHP_EOL;
    // List all users
    $users = App\Models\MasterUser::all(['kodedivisi', 'username', 'nama']);
    echo "All users:" . PHP_EOL;
    foreach ($users as $u) {
        echo "- " . $u->kodedivisi . "/" . $u->username . " - " . $u->nama . PHP_EOL;
    }
}
