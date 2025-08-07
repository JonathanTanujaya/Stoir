<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\MDokumen;

echo "Checking MDokumen model and data...\n\n";

// Check table structure
echo "Primary Key: " . (new MDokumen())->getKeyName() . "\n";
echo "Table: " . (new MDokumen())->getTable() . "\n\n";

// Check first few records
echo "Sample records:\n";
try {
    $docs = MDokumen::take(5)->get();
    if ($docs->count() > 0) {
        foreach ($docs as $doc) {
            echo "- Key: {$doc->getKey()}\n";
            echo "  All attributes: " . json_encode($doc->getAttributes()) . "\n";
        }
    } else {
        echo "No records found in MDokumen table\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\nDone.\n";
