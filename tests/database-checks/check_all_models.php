<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Checking all master data tables with their record counts...\n\n";

try {
    echo "=== CHECKING MASTER DATA TABLES ===\n";
    
    // List of potential table patterns to check
    $tablePatterns = [
        'cust' => ['m_cust', 'd_cust'],
        'sales' => ['m_sales', 'd_sales'],
        'barang' => ['m_barang', 'd_barang'],
        'area' => ['m_area', 'd_area'],
        'kategori' => ['m_kategori', 'd_kategori'],
        'supplier' => ['m_supplier', 'd_supplier'],
        'bank' => ['m_bank', 'd_bank'],
        'coa' => ['m_coa', 'd_coa', 'coa'],
        'divisi' => ['m_divisi', 'd_divisi'],
        'user' => ['master_user', 'm_user', 'd_user'],
        'dokumen' => ['m_dokumen', 'd_dokumen'],
        'resi' => ['m_resi', 'd_resi'],
        'trans' => ['m_trans', 'd_trans'],
        'voucher' => ['m_voucher', 'd_voucher']
    ];
    
    foreach ($tablePatterns as $entity => $tables) {
        echo "\n--- {$entity} TABLES ---\n";
        $bestTable = null;
        $maxCount = 0;
        
        foreach ($tables as $tableName) {
            try {
                $count = \DB::table("dbo.{$tableName}")->count();
                echo "dbo.{$tableName}: {$count} records";
                
                if ($count > 0) {
                    echo " ✅";
                    if ($count > $maxCount) {
                        $maxCount = $count;
                        $bestTable = $tableName;
                    }
                    
                    // Show sample columns
                    $sample = \DB::table("dbo.{$tableName}")->first();
                    if ($sample) {
                        $columns = array_keys((array)$sample);
                        echo " | Columns: " . implode(', ', array_slice($columns, 0, 5));
                    }
                }
                echo "\n";
                
            } catch (\Exception $e) {
                echo "dbo.{$tableName}: Table not found\n";
            }
        }
        
        if ($bestTable) {
            echo "🎯 RECOMMENDED: Use dbo.{$bestTable} ({$maxCount} records)\n";
        } else {
            echo "❌ No data found in any {$entity} table\n";
        }
    }
    
    echo "\n=== CURRENT MODEL MAPPING CHECK ===\n";
    
    // Check current model mappings
    $models = [
        'MCust' => 'app/Models/MCust.php',
        'MSales' => 'app/Models/MSales.php', 
        'MBarang' => 'app/Models/MBarang.php',
        'MArea' => 'app/Models/MArea.php',
        'MKategori' => 'app/Models/MKategori.php',
        'MSupplier' => 'app/Models/MSupplier.php',
        'MBank' => 'app/Models/MBank.php',
        'MCOA' => 'app/Models/MCOA.php',
        'MDivisi' => 'app/Models/MDivisi.php',
        'MasterUser' => 'app/Models/MasterUser.php'
    ];
    
    foreach ($models as $modelName => $filePath) {
        if (file_exists($filePath)) {
            $content = file_get_contents($filePath);
            if (preg_match('/protected \$table = [\'"]([^\'"]+)[\'"]/', $content, $matches)) {
                $currentTable = $matches[1];
                echo "{$modelName}: Currently using '{$currentTable}'\n";
                
                // Check if this table has data
                try {
                    $count = \DB::table($currentTable)->count();
                    echo "  → {$count} records " . ($count > 0 ? "✅" : "❌") . "\n";
                } catch (\Exception $e) {
                    echo "  → Error accessing table ❌\n";
                }
            }
        }
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\nDone.\n";
