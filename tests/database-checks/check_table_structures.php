<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== CHECKING TABLE STRUCTURES ===\n\n";

$tables = [
    'dbo.invoice',
    'dbo.invoice_bonus', 
    'dbo.part_penerimaan',
    'dbo.penerimaan_finance',
    'dbo.journal',
    'dbo.kartu_stok'
];

foreach ($tables as $table) {
    echo "Table: {$table}\n";
    try {
        $columns = DB::select("SELECT column_name FROM information_schema.columns WHERE table_schema = 'dbo' AND table_name = ?", [str_replace('dbo.', '', $table)]);
        echo "Columns:\n";
        foreach ($columns as $column) {
            echo "- {$column->column_name}\n";
        }
        
        // Get sample data
        $sample = DB::select("SELECT * FROM {$table} LIMIT 1");
        if (!empty($sample)) {
            echo "Sample data keys: " . implode(', ', array_keys((array)$sample[0])) . "\n";
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
    echo "\n";
}

echo "=== END CHECK ===\n";
