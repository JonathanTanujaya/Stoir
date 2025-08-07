<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\MArea;
use App\Models\MSales;
use App\Models\MCust;

echo "Checking dummy area records for cleanup...\n\n";

$dummyAreas = ['A3683', 'A1859', 'A0506', 'A1857'];

foreach ($dummyAreas as $kodeArea) {
    $area = MArea::where('kodearea', $kodeArea)->first();
    
    if ($area) {
        echo "Area: {$kodeArea} - {$area->area}\n";
        
        // Check for related records
        $salesCount = MSales::where('kodearea', $kodeArea)->count();
        $customerCount = MCust::where('kodearea', $kodeArea)->count();
        
        echo "Related records - Sales: {$salesCount}, Customers: {$customerCount}\n";
        
        if ($salesCount == 0 && $customerCount == 0) {
            echo "No related records found. Deleting...\n";
            $area->delete();
            echo "✓ Deleted {$kodeArea}\n";
        } else {
            echo "⚠ Cannot delete {$kodeArea} - has related records\n";
        }
        echo "---\n";
    } else {
        echo "Area {$kodeArea} not found (may already be deleted)\n---\n";
    }
}

echo "\nChecking for other dummy data patterns...\n\n";

// Check for other dummy data patterns
$allAreas = MArea::where('area', 'like', '%New Area%')->get();
echo "Areas with 'New Area' pattern: " . $allAreas->count() . "\n";
foreach ($allAreas as $area) {
    echo "- {$area->kodearea}: {$area->area}\n";
}

echo "\nDummy data cleanup completed.\n";
