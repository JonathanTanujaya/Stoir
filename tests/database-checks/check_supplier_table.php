<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\MSupplier;
use Illuminate\Support\Facades\DB;

echo "Checking supplier table and data...\n\n";

// Check current model table
echo "Current model table: " . (new MSupplier())->getTable() . "\n\n";

// Check if m_supplier table exists and has data
echo "Checking m_supplier table:\n";
try {
    $count = DB::table('m_supplier')->count();
    echo "Records in m_supplier: {$count}\n";
    
    if ($count > 0) {
        $sample = DB::table('m_supplier')->take(3)->get();
        echo "Sample data from m_supplier:\n";
        foreach ($sample as $item) {
            echo "- " . json_encode($item) . "\n";
        }
    }
} catch (Exception $e) {
    echo "Error accessing m_supplier: " . $e->getMessage() . "\n";
}

echo "\n";

// Check PostgreSQL schema information
echo "Checking available supplier tables:\n";
try {
    $tables = DB::select("SELECT schemaname, tablename FROM pg_tables WHERE tablename LIKE '%supplier%'");
    foreach ($tables as $table) {
        echo "- {$table->schemaname}.{$table->tablename}\n";
        
        // Check record count for each found table
        try {
            $count = DB::table($table->tablename)->count();
            echo "  Records: {$count}\n";
            
            if ($count > 0) {
                $sample = DB::table($table->tablename)->first();
                echo "  Sample: " . json_encode($sample) . "\n";
            }
        } catch (Exception $e) {
            echo "  Error: " . $e->getMessage() . "\n";
        }
    }
} catch (Exception $e) {
    echo "Error checking tables: " . $e->getMessage() . "\n";
}

echo "\nDone.\n";
