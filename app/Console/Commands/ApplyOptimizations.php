<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ApplyOptimizations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'performance:apply-optimizations 
                          {--model= : Specific model to optimize}
                          {--dry-run : Show what would be optimized without applying changes}
                          {--force : Apply optimizations without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Apply N+1 query optimizations from suggestions file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $force = $this->option('force');
        $specificModel = $this->option('model');

        $this->info("🔧 Applying Performance Optimizations");
        
        if ($dryRun) {
            $this->comment("🔍 DRY RUN MODE - No changes will be applied");
        }
        
        $this->newLine();

        // Load optimization suggestions
        $suggestionsPath = storage_path('optimization_suggestions.json');
        
        if (!File::exists($suggestionsPath)) {
            $this->error("❌ Optimization suggestions file not found!");
            $this->comment("Run 'php artisan performance:optimize' first to generate suggestions");
            return Command::FAILURE;
        }

        $suggestions = json_decode(File::get($suggestionsPath), true);
        
        if (!$suggestions || empty($suggestions['n_plus_one_fixes'])) {
            $this->warn("⚠️ No N+1 optimization suggestions found");
            return Command::SUCCESS;
        }

        $this->info("📋 Found " . count($suggestions['n_plus_one_fixes']) . " optimization suggestions");
        $this->newLine();

        $appliedOptimizations = 0;

        foreach ($suggestions['n_plus_one_fixes'] as $fix) {
            $modelPath = $this->getModelPath($fix['model']);
            
            if ($specificModel && !str_contains($fix['model'], $specificModel)) {
                continue;
            }

            if (!File::exists($modelPath)) {
                $this->warn("⚠️ Model file not found: " . $modelPath);
                continue;
            }

            $this->info("🔍 Analyzing: " . class_basename($fix['model']));
            $this->line("   Relation: " . $fix['relation'] . " (" . $fix['type'] . ")");

            if ($this->applyOptimization($modelPath, $fix, $dryRun, $force)) {
                $appliedOptimizations++;
                $this->line("   ✅ Applied optimization");
            } else {
                $this->line("   ⏭️ Skipped");
            }

            $this->newLine();
        }

        $this->info("✅ Optimization application completed!");
        $this->comment("Applied {$appliedOptimizations} optimizations");

        if (!$dryRun && $appliedOptimizations > 0) {
            $this->newLine();
            $this->comment("💡 Next steps:");
            $this->line("• Run 'php artisan performance:analyze' to verify improvements");
            $this->line("• Test your application to ensure everything works correctly");
            $this->line("• Update your controllers to use the optimized eager loading");
        }

        return Command::SUCCESS;
    }

    private function getModelPath(string $modelClass): string
    {
        $modelName = class_basename($modelClass);
        return app_path("Models/{$modelName}.php");
    }

    private function applyOptimization(string $modelPath, array $fix, bool $dryRun, bool $force): bool
    {
        $content = File::get($modelPath);
        $relationName = $fix['relation'];
        $relationType = $fix['type'];

        // Skip generic relationship method names that aren't actual relations
        $genericMethods = ['hasOne', 'hasMany', 'belongsTo', 'belongsToMany', 'morphTo', 'morphMany'];
        if (in_array($relationName, $genericMethods)) {
            return false;
        }

        // Check if optimization is already applied
        if ($this->isOptimizationAlreadyApplied($content, $relationName)) {
            $this->line("   ✓ Already optimized");
            return false;
        }

        if ($dryRun) {
            $this->line("   Would add eager loading for '{$relationName}' relation");
            return true;
        }

        if (!$force && !$this->confirm("Apply optimization for '{$relationName}' relation?")) {
            return false;
        }

        // Apply the optimization
        $optimizedContent = $this->addEagerLoading($content, $relationName);
        
        if ($optimizedContent !== $content) {
            File::put($modelPath, $optimizedContent);
            return true;
        }

        return false;
    }

    private function isOptimizationAlreadyApplied(string $content, string $relationName): bool
    {
        // Check if $with array already includes this relation
        $patterns = [
            "/protected\s+\\\$with\s*=\s*\[[^\]]*['\"]" . preg_quote($relationName, '/') . "['\"][^\]]*\]/",
            "/protected\s+\\\$with\s*=\s*\[[^\]]*'" . preg_quote($relationName, '/') . "'[^\]]*\]/",
            "/protected\s+\\\$with\s*=\s*\[[^\]]*\"" . preg_quote($relationName, '/') . "\"[^\]]*\]/"
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $content)) {
                return true;
            }
        }

        return false;
    }

    private function addEagerLoading(string $content, string $relationName): string
    {
        // Look for existing $with property
        if (preg_match('/protected\s+\$with\s*=\s*\[([^\]]*)\];/', $content, $matches)) {
            // Update existing $with array
            $existingRelations = $matches[1];
            $existingRelations = trim($existingRelations);
            
            if (empty($existingRelations)) {
                $newWith = "protected \$with = ['{$relationName}'];";
            } else {
                $newWith = "protected \$with = [{$existingRelations}, '{$relationName}'];";
            }
            
            $content = preg_replace(
                '/protected\s+\$with\s*=\s*\[[^\]]*\];/',
                $newWith,
                $content
            );
        } else {
            // Add new $with property after $fillable or class opening
            $insertAfterPatterns = [
                '/protected\s+\$fillable\s*=\s*\[[^\]]*\];/',
                '/protected\s+\$guarded\s*=\s*\[[^\]]*\];/',
                '/use\s+HasFactory;/',
                '/class\s+\w+\s+extends\s+Model\s*\{/'
            ];

            $inserted = false;
            foreach ($insertAfterPatterns as $pattern) {
                if (preg_match($pattern, $content, $matches, PREG_OFFSET_CAPTURE)) {
                    $insertPosition = $matches[0][1] + strlen($matches[0][0]);
                    $newProperty = "\n\n    /**\n     * The relationships that should always be loaded.\n     */\n    protected \$with = ['{$relationName}'];";
                    
                    $content = substr_replace($content, $newProperty, $insertPosition, 0);
                    $inserted = true;
                    break;
                }
            }

            if (!$inserted) {
                // Fallback: add after opening brace
                $content = preg_replace(
                    '/(class\s+\w+\s+extends\s+Model\s*\{\s*)/',
                    '$1' . "\n    /**\n     * The relationships that should always be loaded.\n     */\n    protected \$with = ['{$relationName}'];\n",
                    $content
                );
            }
        }

        return $content;
    }
}
