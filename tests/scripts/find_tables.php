<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== FINDING ACTUAL TABLE NAMES ===\n\n";

// Get all tables in dbo schema
$tables = DB::select("SELECT table_name FROM information_schema.tables WHERE table_schema = 'dbo' ORDER BY table_name");

echo "Available tables in dbo schema:\n";
foreach ($tables as $table) {
    echo "- dbo.{$table->table_name}\n";
}

echo "\n=== CHECKING SPECIFIC PATTERNS ===\n";

// Look for specific patterns
$patterns = ['invoice', 'bonus', 'penerimaan', 'journal', 'kartu', 'stok'];

foreach ($patterns as $pattern) {
    echo "\nTables containing '{$pattern}':\n";
    foreach ($tables as $table) {
        if (strpos(strtolower($table->table_name), $pattern) !== false) {
            echo "- dbo.{$table->table_name}\n";
        }
    }
}

echo "\n=== END CHECK ===\n";
