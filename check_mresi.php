<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Checking m_resi table structure...\n\n";

try {
    $sample = DB::table('m_resi')->first();
    if ($sample) {
        echo "Sample record from m_resi:\n";
        echo json_encode($sample, JSON_PRETTY_PRINT) . "\n\n";
        
        echo "Columns found:\n";
        foreach ($sample as $key => $value) {
            echo "- {$key}: " . gettype($value) . " (" . (is_null($value) ? 'NULL' : $value) . ")\n";
        }
    } else {
        echo "No data found in m_resi table\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\nDone.\n";
