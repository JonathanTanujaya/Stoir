<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Analyzing remaining models that need enhancement...\n\n";

// Check models that exist but might need enhancement
$modelsToAnalyze = [
    'ReturPenerimaanDetail',
    'UserModule', 
    'MModule',
    'MVoucher',
    'Opname',
    'OpnameDetail',
    'ClaimPenjualan',
    'ClaimPenjualanDetail',
    'MergeBarang',
    'MergeBarangDetail',
    'StokClaim'
];

foreach ($modelsToAnalyze as $modelName) {
    echo "=== {$modelName} ===\n";
    try {
        $modelClass = "App\\Models\\{$modelName}";
        if (class_exists($modelClass)) {
            $model = new $modelClass();
            echo "Table: " . $model->getTable() . "\n";
            
            // Try to get count
            try {
                $count = $modelClass::count();
                echo "Record count: {$count}\n";
            } catch (Exception $e) {
                echo "Count error: " . $e->getMessage() . "\n";
            }
            
            // Try to get first record
            try {
                $first = $modelClass::first();
                if ($first) {
                    echo "Sample: " . substr(json_encode($first->toArray()), 0, 200) . "...\n";
                } else {
                    echo "No records found\n";
                }
            } catch (Exception $e) {
                echo "Query error: " . $e->getMessage() . "\n";
            }
        } else {
            echo "Model class not found\n";
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
    echo "\n";
}

echo "Analysis complete.\n";
