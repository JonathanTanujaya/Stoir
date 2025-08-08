<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== CHECKING BANK DATA ===\n\n";

// Check both m_bank and d_bank tables
$tables = ['dbo.m_bank', 'dbo.d_bank'];

foreach ($tables as $table) {
    echo "Checking {$table}:\n";
    try {
        $count = DB::select("SELECT COUNT(*) as total FROM {$table}")[0]->total;
        echo "- Total records: {$count}\n";
        
        if ($count > 0) {
            $sample = DB::select("SELECT * FROM {$table} LIMIT 3");
            echo "- Sample data:\n";
            foreach ($sample as $row) {
                if (isset($row->kodebank)) {
                    echo "  * {$row->kodedivisi} | {$row->kodebank} | " . (isset($row->namabank) ? $row->namabank : 'N/A') . "\n";
                }
            }
        }
    } catch (Exception $e) {
        echo "- Error: " . $e->getMessage() . "\n";
    }
    echo "\n";
}

echo "=== END CHECK ===\n";
