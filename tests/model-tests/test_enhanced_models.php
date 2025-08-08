<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\UserModule;
use App\Models\MModule;
use App\Models\ReturPenerimaanDetail;
use App\Models\MVoucher;
use App\Models\Opname;
use App\Models\OpnameDetail;

echo "Testing enhanced models...\n\n";

$modelsToTest = [
    'UserModule' => UserModule::class,
    'MModule' => MModule::class,
    'ReturPenerimaanDetail' => ReturPenerimaanDetail::class,
    'MVoucher' => MVoucher::class,
    'Opname' => Opname::class,
    'OpnameDetail' => OpnameDetail::class,
];

foreach ($modelsToTest as $name => $class) {
    echo "=== {$name} ===\n";
    try {
        $count = $class::count();
        echo "Total records: {$count}\n";
        
        if ($count > 0) {
            $sample = $class::first();
            echo "Sample: " . substr(json_encode($sample->toArray()), 0, 150) . "...\n";
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
    echo "\n";
}

echo "Testing complete.\n";
