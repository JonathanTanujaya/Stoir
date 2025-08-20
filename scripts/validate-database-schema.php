<?php

/**
 * Database Schema Standardization Validation Script
 * Memvalidasi semua Laravel models menggunakan dbo. prefix yang konsisten
 */

class DatabaseSchemaValidator 
{
    private $modelsPath;
    private $results = [];
    
    public function __construct()
    {
        $this->modelsPath = __DIR__ . '/../app/Models';
    }
    
    public function validateAllModels()
    {
        echo "🔍 VALIDATING DATABASE SCHEMA STANDARDIZATION\n";
        echo str_repeat("=", 60) . "\n";
        
        $modelFiles = glob($this->modelsPath . '/*.php');
        $totalModels = count($modelFiles);
        
        $validModels = 0;
        $invalidModels = 0;
        $missingTable = 0;
        
        foreach ($modelFiles as $modelFile) {
            $modelName = basename($modelFile, '.php');
            $content = file_get_contents($modelFile);
            
            // Check if model has protected $table
            if (preg_match('/protected\s+\$table\s*=\s*[\'"]([^\'"]+)[\'"];/', $content, $matches)) {
                $tableName = $matches[1];
                
                // Check if table has dbo. prefix
                if (strpos($tableName, 'dbo.') === 0) {
                    echo "✅ {$modelName}: {$tableName}\n";
                    $validModels++;
                    $this->results['valid'][] = [
                        'model' => $modelName,
                        'table' => $tableName
                    ];
                } else {
                    echo "❌ {$modelName}: {$tableName} (MISSING dbo. prefix)\n";
                    $invalidModels++;
                    $this->results['invalid'][] = [
                        'model' => $modelName,
                        'table' => $tableName,
                        'should_be' => 'dbo.' . $tableName
                    ];
                }
            } else {
                echo "⚠️  {$modelName}: No table definition found\n";
                $missingTable++;
                $this->results['missing_table'][] = $modelName;
            }
        }
        
        echo "\n" . str_repeat("=", 60) . "\n";
        echo "📊 VALIDATION SUMMARY:\n";
        echo "Total Models: {$totalModels}\n";
        echo "✅ Valid (with dbo. prefix): {$validModels}\n";
        echo "❌ Invalid (missing dbo. prefix): {$invalidModels}\n";
        echo "⚠️  Missing table definition: {$missingTable}\n";
        
        $successRate = round(($validModels / $totalModels) * 100, 2);
        echo "\n🎯 Success Rate: {$successRate}%\n";
        
        if ($invalidModels > 0) {
            echo "\n🔧 FIXES NEEDED:\n";
            foreach ($this->results['invalid'] as $invalid) {
                echo "- {$invalid['model']}: Change '{$invalid['table']}' to '{$invalid['should_be']}'\n";
            }
        }
        
        if ($successRate >= 95) {
            echo "\n🎉 SCHEMA STANDARDIZATION: EXCELLENT!\n";
        } elseif ($successRate >= 80) {
            echo "\n✅ SCHEMA STANDARDIZATION: GOOD\n";
        } else {
            echo "\n⚠️  SCHEMA STANDARDIZATION: NEEDS IMPROVEMENT\n";
        }
        
        return $this->results;
    }
    
    public function generateFixScript()
    {
        if (empty($this->results['invalid'])) {
            echo "\n✅ No fixes needed - all models are properly standardized!\n";
            return;
        }
        
        echo "\n🔧 GENERATING AUTO-FIX SCRIPT:\n";
        echo str_repeat("-", 40) . "\n";
        
        foreach ($this->results['invalid'] as $invalid) {
            $modelFile = $this->modelsPath . "/{$invalid['model']}.php";
            $oldPattern = "protected \$table = '{$invalid['table']}';";
            $newPattern = "protected \$table = '{$invalid['should_be']}';";
            
            echo "sed -i 's/{$oldPattern}/{$newPattern}/g' {$modelFile}\n";
        }
    }
}

// Run validation if script is called directly
if (php_sapi_name() === 'cli') {
    $validator = new DatabaseSchemaValidator();
    $results = $validator->validateAllModels();
    $validator->generateFixScript();
}
