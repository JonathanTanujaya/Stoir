<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Checking master_user table structure...\n";

try {
    // Check if table exists and its structure
    $columns = DB::select("
        SELECT column_name, data_type, character_maximum_length 
        FROM information_schema.columns 
        WHERE table_schema = 'dbo' AND table_name = 'master_user'
        ORDER BY ordinal_position
    ");
    
    if (empty($columns)) {
        echo "❌ Table dbo.master_user not found\n";
        echo "Checking if table exists in public schema...\n";
        
        $publicColumns = DB::select("
            SELECT column_name, data_type, character_maximum_length 
            FROM information_schema.columns 
            WHERE table_schema = 'public' AND table_name = 'master_user'
            ORDER BY ordinal_position
        ");
        
        if (!empty($publicColumns)) {
            echo "✅ Found table in public schema:\n";
            foreach ($publicColumns as $col) {
                echo "  - {$col->column_name}: {$col->data_type}";
                if ($col->character_maximum_length) {
                    echo "({$col->character_maximum_length})";
                }
                echo "\n";
            }
            echo "\n⚠️ Update MasterUser model to use 'public.master_user' or 'master_user'\n";
        } else {
            echo "❌ Table master_user not found in any schema\n";
        }
    } else {
        echo "✅ Found table in dbo schema:\n";
        foreach ($columns as $col) {
            echo "  - {$col->column_name}: {$col->data_type}";
            if ($col->character_maximum_length) {
                echo "({$col->character_maximum_length})";
            }
            echo "\n";
        }
    }
    
    // Check existing data
    echo "\nChecking existing data...\n";
    try {
        $count = DB::table('dbo.master_user')->count();
        echo "Records in dbo.master_user: {$count}\n";
        
        if ($count > 0) {
            $sample = DB::table('dbo.master_user')->first();
            echo "Sample record: " . json_encode($sample) . "\n";
        }
    } catch (Exception $e) {
        try {
            $count = DB::table('master_user')->count();
            echo "Records in master_user (public): {$count}\n";
            
            if ($count > 0) {
                $sample = DB::table('master_user')->first();
                echo "Sample record: " . json_encode($sample) . "\n";
            }
        } catch (Exception $e2) {
            echo "❌ Cannot access master_user table: " . $e2->getMessage() . "\n";
        }
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\nDone.\n";
