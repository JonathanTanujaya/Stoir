<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== CHECKING ACTUAL CASE SENSITIVE COLUMNS ===\n\n";

$tables = [
    'invoice',
    'partpenerimaan', 
    'penerimaanfinance',
    'journal',
    'kartustok'
];

foreach ($tables as $table) {
    echo "Table: dbo.{$table}\n";
    try {
        $sample = DB::select("SELECT * FROM dbo.{$table} LIMIT 1");
        if (!empty($sample)) {
            echo "Actual column names:\n";
            foreach (array_keys((array)$sample[0]) as $column) {
                echo "- {$column}\n";
            }
        } else {
            echo "No data in table\n";
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
    echo "\n";
}

echo "=== END CHECK ===\n";
