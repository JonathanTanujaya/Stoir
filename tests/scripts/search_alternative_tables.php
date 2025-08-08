<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Searching for alternative table names that might contain data...\n\n";

try {
    // Get all tables in dbo schema
    $allTables = \DB::select("SELECT table_name FROM information_schema.tables WHERE table_schema = 'dbo' ORDER BY table_name");
    
    echo "=== ALL TABLES WITH RECORD COUNTS ===\n";
    foreach ($allTables as $table) {
        try {
            $count = \DB::table("dbo.{$table->table_name}")->count();
            if ($count > 0) {
                echo "dbo.{$table->table_name}: {$count} records ✅\n";
                
                // Show sample columns for tables with data
                $sample = \DB::table("dbo.{$table->table_name}")->first();
                if ($sample) {
                    $columns = array_keys((array)$sample);
                    echo "  Columns: " . implode(', ', array_slice($columns, 0, 8)) . "\n";
                }
            } else {
                echo "dbo.{$table->table_name}: {$count} records\n";
            }
        } catch (\Exception $e) {
            echo "dbo.{$table->table_name}: Error - {$e->getMessage()}\n";
        }
    }
    
    echo "\n=== TABLES THAT MIGHT BE CUSTOMER/SALES RELATED ===\n";
    $keywords = ['cust', 'customer', 'client', 'sales', 'salesman', 'area', 'region', 'coa', 'account'];
    
    foreach ($allTables as $table) {
        $tableName = strtolower($table->table_name);
        foreach ($keywords as $keyword) {
            if (strpos($tableName, $keyword) !== false) {
                try {
                    $count = \DB::table("dbo.{$table->table_name}")->count();
                    echo "dbo.{$table->table_name}: {$count} records";
                    if ($count > 0) echo " ✅";
                    echo "\n";
                } catch (\Exception $e) {
                    echo "dbo.{$table->table_name}: Error\n";
                }
                break;
            }
        }
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\nDone.\n";
