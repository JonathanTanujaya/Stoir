<?php

require_once __DIR__ . '/../../vendor/autoload.php';

$app = require __DIR__ . '/../../bootstrap/app.php';
$app->boot();

use Illuminate\Support\Facades\DB;

echo "=== KARTUSTOK TABLE STRUCTURE ===\n\n";

try {
    $columns = DB::select("
        SELECT column_name, data_type, is_nullable 
        FROM information_schema.columns 
        WHERE table_schema = 'dbo' 
        AND table_name = 'kartustok'
        ORDER BY ordinal_position
    ");

    if (empty($columns)) {
        echo "❌ Table kartustok not found in schema dbo\n";
        
        // Check if table exists in any schema
        echo "\nChecking all schemas...\n";
        $allTables = DB::select("
            SELECT table_schema, table_name 
            FROM information_schema.tables 
            WHERE table_name LIKE '%kartustok%'
        ");
        
        if (empty($allTables)) {
            echo "❌ Table kartustok not found in any schema\n";
        } else {
            echo "📋 Found similar tables:\n";
            foreach ($allTables as $table) {
                echo "   - {$table->table_schema}.{$table->table_name}\n";
            }
        }
    } else {
        echo "✅ Table dbo.kartustok found!\n\n";
        echo "📋 Columns:\n";
        foreach ($columns as $col) {
            echo sprintf("   %-20s %-15s %s\n", 
                $col->column_name, 
                $col->data_type,
                $col->is_nullable === 'YES' ? 'NULL' : 'NOT NULL'
            );
        }
        
        echo "\n📊 Sample data (first 3 rows):\n";
        $sample = DB::select("SELECT * FROM dbo.kartustok LIMIT 3");
        if (!empty($sample)) {
            $first = $sample[0];
            foreach ($first as $key => $value) {
                echo "   $key: " . ($value ?? 'NULL') . "\n";
            }
        } else {
            echo "   No data found in table\n";
        }
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n=== DONE ===\n";
