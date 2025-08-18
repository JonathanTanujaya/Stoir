<?php

namespace App\Console\Commands;

use App\Services\PerformanceOptimizationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class OptimizePerformance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'performance:optimize 
                          {--type=all : Type of optimization (all, cache, queries, indexes)}
                          {--force : Force optimization without confirmation}
                          {--dry-run : Show what would be optimized without applying changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Apply performance optimizations to the StockFlow system';

    protected PerformanceOptimizationService $performanceService;

    public function __construct(PerformanceOptimizationService $performanceService)
    {
        parent::__construct();
        $this->performanceService = $performanceService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $type = $this->option('type');
        $force = $this->option('force');
        $dryRun = $this->option('dry-run');

        $this->info("🚀 Starting Performance Optimization for StockFlow System");
        
        if ($dryRun) {
            $this->comment("🔍 DRY RUN MODE - No changes will be applied");
        }
        
        $this->newLine();

        $optimizationsApplied = 0;

        if ($type === 'all' || $type === 'cache') {
            $optimizationsApplied += $this->optimizeCache($force, $dryRun);
        }

        if ($type === 'all' || $type === 'queries') {
            $optimizationsApplied += $this->optimizeQueries($force, $dryRun);
        }

        if ($type === 'all' || $type === 'indexes') {
            $optimizationsApplied += $this->optimizeIndexes($force, $dryRun);
        }

        $this->newLine();
        $this->info("✅ Performance optimization completed!");
        $this->comment("Applied {$optimizationsApplied} optimizations");
        
        if (!$dryRun) {
            $this->comment("Run 'php artisan performance:analyze' to see the improvements");
        }

        return Command::SUCCESS;
    }

    private function optimizeCache($force = false, $dryRun = false): int
    {
        $this->info("🗄️ Optimizing Cache Performance...");
        $optimizations = 0;

        // Clear and warm cache
        if (!$dryRun) {
            if ($force || $this->confirm('Clear and warm application cache?')) {
                Cache::flush();
                $this->line("✓ Cache cleared");
                
                // Warm critical cache data
                $this->warmCache();
                $this->line("✓ Cache warmed with critical data");
                $optimizations++;
            }
        } else {
            $this->line("Would clear and warm application cache");
            $optimizations++;
        }

        // Optimize config cache
        if (!$dryRun) {
            if ($force || $this->confirm('Optimize configuration cache?')) {
                $this->call('config:cache');
                $this->line("✓ Configuration cached");
                $optimizations++;
            }
        } else {
            $this->line("Would optimize configuration cache");
            $optimizations++;
        }

        // Optimize route cache
        if (!$dryRun) {
            if ($force || $this->confirm('Optimize route cache?')) {
                $this->call('route:cache');
                $this->line("✓ Routes cached");
                $optimizations++;
            }
        } else {
            $this->line("Would optimize route cache");
            $optimizations++;
        }

        return $optimizations;
    }

    private function optimizeQueries($force = false, $dryRun = false): int
    {
        $this->info("📊 Optimizing Database Queries...");
        $optimizations = 0;

        // Get N+1 query issues
        $nPlusOneIssues = $this->performanceService->detectNPlusOneQueries();
        
        if (count($nPlusOneIssues) > 0) {
            $this->warn("Found " . count($nPlusOneIssues) . " potential N+1 query issues:");
            
            foreach (array_slice($nPlusOneIssues, 0, 5) as $issue) {
                $this->line("• Model: {$issue['model']} -> Relation: {$issue['relation']}");
                $this->comment("  Recommendation: {$issue['recommendation']}");
            }

            if (!$dryRun) {
                if ($force || $this->confirm('Create optimization suggestions file?')) {
                    $this->createOptimizationSuggestions($nPlusOneIssues);
                    $this->line("✓ Created optimization suggestions in storage/optimization_suggestions.json");
                    $optimizations++;
                }
            } else {
                $this->line("Would create optimization suggestions file");
                $optimizations++;
            }
        }

        return $optimizations;
    }

    private function optimizeIndexes($force = false, $dryRun = false): int
    {
        $this->info("📇 Optimizing Database Indexes...");
        $optimizations = 0;

        $missingIndexes = $this->performanceService->findMissingIndexes();
        
        if (count($missingIndexes) > 0) {
            $this->warn("Found " . count($missingIndexes) . " missing indexes:");
            
            foreach (array_slice($missingIndexes, 0, 5) as $index) {
                $this->line("• Table: {$index['table']}, Column: {$index['column']}, Impact: {$index['impact']}");
            }

            if (!$dryRun) {
                if ($force || $this->confirm('Create migration for missing indexes?')) {
                    $this->createIndexMigration($missingIndexes);
                    $this->line("✓ Created index migration");
                    $optimizations++;
                }
            } else {
                $this->line("Would create migration for missing indexes");
                $optimizations++;
            }
        } else {
            $this->line("✓ No missing indexes found");
        }

        return $optimizations;
    }

    private function warmCache(): void
    {
        // Cache commonly used master data
        try {
            // Cache companies if table exists
            if (\Schema::hasTable('companies')) {
                $companies = DB::table('companies')->select('id', 'name')->get();
                Cache::put('master_companies', $companies, now()->addHours(6));
            }

            // Cache basic configuration
            Cache::put('app_config', [
                'name' => config('app.name'),
                'version' => '1.0.0',
                'timezone' => config('app.timezone')
            ], now()->addHours(12));

        } catch (\Exception $e) {
            $this->warn("Warning: Could not warm all cache data - " . $e->getMessage());
        }
    }

    private function createOptimizationSuggestions(array $issues): void
    {
        $suggestions = [
            'generated_at' => now()->toDateTimeString(),
            'total_issues' => count($issues),
            'n_plus_one_fixes' => []
        ];

        foreach ($issues as $issue) {
            $modelName = class_basename($issue['model']);
            $relationName = $issue['relation'];
            
            $suggestions['n_plus_one_fixes'][] = [
                'model' => $issue['model'],
                'relation' => $relationName,
                'type' => $issue['type'],
                'recommendation' => $issue['recommendation'],
                'example_fix' => "// In your controller:\n" .
                               "{$modelName}::with('{$relationName}')->get();\n\n" .
                               "// Or in your model relationship method:\n" .
                               "protected \$with = ['{$relationName}'];"
            ];
        }

        file_put_contents(
            storage_path('optimization_suggestions.json'),
            json_encode($suggestions, JSON_PRETTY_PRINT)
        );
    }

    private function createIndexMigration(array $indexes): void
    {
        $timestamp = date('Y_m_d_His');
        $migrationName = "add_performance_indexes_{$timestamp}";
        $migrationPath = database_path("migrations/{$timestamp}_add_performance_indexes.php");

        $migrationContent = "<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
";

        foreach ($indexes as $index) {
            $migrationContent .= "        // Add index for {$index['table']}.{$index['column']} - Impact: {$index['impact']}\n";
            $migrationContent .= "        if (Schema::hasTable('{$index['table']}') && Schema::hasColumn('{$index['table']}', '{$index['column']}')) {\n";
            $migrationContent .= "            Schema::table('{$index['table']}', function (Blueprint \$table) {\n";
            $migrationContent .= "                \$table->index('{$index['column']}', 'idx_{$index['table']}_{$index['column']}');\n";
            $migrationContent .= "            });\n";
            $migrationContent .= "        }\n\n";
        }

        $migrationContent .= "    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
";

        foreach ($indexes as $index) {
            $migrationContent .= "        if (Schema::hasTable('{$index['table']}')) {\n";
            $migrationContent .= "            Schema::table('{$index['table']}', function (Blueprint \$table) {\n";
            $migrationContent .= "                \$table->dropIndex('idx_{$index['table']}_{$index['column']}');\n";
            $migrationContent .= "            });\n";
            $migrationContent .= "        }\n\n";
        }

        $migrationContent .= "    }
};";

        file_put_contents($migrationPath, $migrationContent);
    }
}
