<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== CHECKING CORRECT TABLE STRUCTURES ===\n\n";

$tables = [
    'dbo.invoice',
    'dbo.partpenerimaan',
    'dbo.penerimaanfinance',
    'dbo.journal',
    'dbo.kartustok'
];

foreach ($tables as $table) {
    echo "Table: {$table}\n";
    try {
        $columns = DB::select("SELECT column_name FROM information_schema.columns WHERE table_schema = 'dbo' AND table_name = ?", [str_replace('dbo.', '', $table)]);
        echo "Columns:\n";
        foreach ($columns as $column) {
            echo "- {$column->column_name}\n";
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
    echo "\n";
}

echo "=== END CHECK ===\n";
