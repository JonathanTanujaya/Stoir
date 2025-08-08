<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Searching for barang-related tables...\n\n";

try {
    echo "=== SEARCHING FOR BARANG TABLES ===\n";
    $barangTables = \DB::select("SELECT table_name FROM information_schema.tables WHERE table_schema = 'dbo' AND table_name LIKE '%barang%'");
    foreach ($barangTables as $table) {
        $count = \DB::table("dbo.{$table->table_name}")->count();
        echo "dbo.{$table->table_name}: {$count} records\n";
        
        if ($count > 0) {
            echo "  Sample columns: ";
            $sample = \DB::table("dbo.{$table->table_name}")->first();
            if ($sample) {
                $columns = array_keys((array)$sample);
                echo implode(', ', array_slice($columns, 0, 5)) . "\n";
            }
        }
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\nDone.\n";
