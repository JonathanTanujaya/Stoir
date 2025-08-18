<?php

namespace App\Console\Commands;

use App\Services\PerformanceOptimizationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AnalyzePerformance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'performance:analyze 
                          {--category=all : Category to analyze (all, queries, cache, api, database)}
                          {--output=console : Output format (console, json, log)}
                          {--detailed : Show detailed analysis}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Analyze application performance and identify optimization opportunities';

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
        $category = $this->option('category');
        $output = $this->option('output');
        $detailed = $this->option('detailed');

        $this->info("🚀 Starting Performance Analysis for StockFlow System");
        $this->newLine();

        $results = [];

        if ($category === 'all' || $category === 'queries') {
            $this->info("📊 Analyzing Database Queries...");
            $results['database'] = $this->analyzeDatabasePerformance($detailed);
        }

        if ($category === 'all' || $category === 'cache') {
            $this->info("🗄️ Analyzing Cache Performance...");
            $results['cache'] = $this->analyzeCachePerformance($detailed);
        }

        if ($category === 'all' || $category === 'api') {
            $this->info("🌐 Analyzing API Performance...");
            $results['api'] = $this->analyzeApiPerformance($detailed);
        }

        if ($category === 'all' || $category === 'database') {
            $this->info("🗃️ Analyzing Database Optimization...");
            $results['optimization'] = $this->analyzeDatabaseOptimization($detailed);
        }

        $this->displayResults($results, $output, $detailed);
        
        return Command::SUCCESS;
    }

    private function analyzeDatabasePerformance($detailed = false)
    {
        try {
            $slowQueries = $this->performanceService->findSlowQueries();
            $nPlusOneIssues = $this->performanceService->detectNPlusOneQueries();

            $this->table(
                ['Type', 'Count', 'Status'],
                [
                    ['Slow Queries (>100ms)', count($slowQueries), count($slowQueries) > 5 ? '⚠️ High' : '✅ Good'],
                    ['N+1 Query Issues', count($nPlusOneIssues), count($nPlusOneIssues) > 0 ? '⚠️ Found' : '✅ None'],
                ]
            );

            if ($detailed && count($slowQueries) > 0) {
                $this->warn("📋 Slow Queries Details:");
                foreach (array_slice($slowQueries, 0, 5) as $query) {
                    $this->line("• " . substr($query['sql'] ?? 'Unknown query', 0, 80) . '...');
                    $this->line("  Time: " . ($query['time'] ?? 'Unknown') . "ms");
                }
            }

            if ($detailed && count($nPlusOneIssues) > 0) {
                $this->warn("🔍 N+1 Query Issues:");
                foreach (array_slice($nPlusOneIssues, 0, 3) as $issue) {
                    $this->line("• Model: " . ($issue['model'] ?? 'Unknown'));
                    $this->line("  Relation: " . ($issue['relation'] ?? 'Unknown'));
                }
            }

            return [
                'slow_queries' => count($slowQueries),
                'n_plus_one_issues' => count($nPlusOneIssues),
                'details' => $detailed ? ['slow_queries' => $slowQueries, 'n_plus_one' => $nPlusOneIssues] : []
            ];

        } catch (\Exception $e) {
            $this->error("Database analysis failed: " . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

    private function analyzeCachePerformance($detailed = false)
    {
        try {
            $cacheStats = $this->performanceService->getCacheStats();
            $hitRate = $cacheStats['hit_rate'] ?? 0;
            
            $this->table(
                ['Metric', 'Value', 'Status'],
                [
                    ['Cache Hit Rate', number_format($hitRate, 2) . '%', $hitRate > 80 ? '✅ Good' : '⚠️ Low'],
                    ['Cache Entries', $cacheStats['entries'] ?? 0, '📊 Info'],
                    ['Memory Usage', $cacheStats['memory'] ?? 'Unknown', '📊 Info'],
                ]
            );

            if ($detailed) {
                $recommendations = $this->performanceService->getCacheRecommendations();
                if (!empty($recommendations)) {
                    $this->warn("💡 Cache Optimization Recommendations:");
                    foreach ($recommendations as $recommendation) {
                        $this->line("• " . $recommendation);
                    }
                }
            }

            return [
                'hit_rate' => $hitRate,
                'stats' => $cacheStats,
                'recommendations' => $detailed ? $this->performanceService->getCacheRecommendations() : []
            ];

        } catch (\Exception $e) {
            $this->error("Cache analysis failed: " . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

    private function analyzeApiPerformance($detailed = false)
    {
        try {
            $endpoints = $this->performanceService->getSlowApiEndpoints();
            $avgResponseTime = collect($endpoints)->avg('avg_response_time') ?? 0;

            $this->table(
                ['Metric', 'Value', 'Status'],
                [
                    ['Slow Endpoints (>500ms)', count($endpoints), count($endpoints) > 10 ? '⚠️ High' : '✅ Good'],
                    ['Avg Response Time', number_format($avgResponseTime, 0) . 'ms', $avgResponseTime > 300 ? '⚠️ Slow' : '✅ Fast'],
                ]
            );

            if ($detailed && count($endpoints) > 0) {
                $this->warn("🐌 Slowest API Endpoints:");
                foreach (array_slice($endpoints, 0, 5) as $endpoint) {
                    $this->line("• " . ($endpoint['route'] ?? 'Unknown'));
                    $this->line("  Avg Time: " . number_format($endpoint['avg_response_time'] ?? 0, 0) . "ms");
                }
            }

            return [
                'slow_endpoints' => count($endpoints),
                'avg_response_time' => $avgResponseTime,
                'details' => $detailed ? $endpoints : []
            ];

        } catch (\Exception $e) {
            $this->error("API analysis failed: " . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

    private function analyzeDatabaseOptimization($detailed = false)
    {
        try {
            $missingIndexes = $this->performanceService->findMissingIndexes();
            $optimizations = $this->performanceService->getOptimizationRecommendations();

            $this->table(
                ['Category', 'Count', 'Priority'],
                [
                    ['Missing Indexes', count($missingIndexes), count($missingIndexes) > 5 ? '🔥 High' : '💡 Medium'],
                    ['Query Optimizations', count($optimizations), count($optimizations) > 10 ? '🔥 High' : '💡 Medium'],
                ]
            );

            if ($detailed && count($missingIndexes) > 0) {
                $this->warn("📇 Recommended Indexes:");
                foreach (array_slice($missingIndexes, 0, 5) as $index) {
                    $this->line("• Table: " . ($index['table'] ?? 'Unknown'));
                    $this->line("  Column: " . ($index['column'] ?? 'Unknown'));
                    $this->line("  Impact: " . ($index['impact'] ?? 'Medium'));
                }
            }

            if ($detailed && count($optimizations) > 0) {
                $this->warn("⚡ Optimization Opportunities:");
                foreach (array_slice($optimizations, 0, 5) as $optimization) {
                    $this->line("• " . ($optimization['description'] ?? 'Unknown optimization'));
                    $this->line("  Impact: " . ($optimization['impact'] ?? 'Medium'));
                }
            }

            return [
                'missing_indexes' => count($missingIndexes),
                'optimizations' => count($optimizations),
                'details' => $detailed ? ['indexes' => $missingIndexes, 'optimizations' => $optimizations] : []
            ];

        } catch (\Exception $e) {
            $this->error("Database optimization analysis failed: " . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

    private function displayResults($results, $output, $detailed)
    {
        $this->newLine();
        $this->info("📈 Performance Analysis Summary");
        $this->line("═══════════════════════════════════════");

        // Calculate overall score
        $score = $this->calculateOverallScore($results);
        $scoreColor = $score >= 80 ? 'info' : ($score >= 60 ? 'comment' : 'error');
        
        $this->newLine();
        $this->{$scoreColor}("🎯 Overall Performance Score: {$score}/100");
        
        // Provide recommendations
        $this->newLine();
        $this->info("💡 Priority Recommendations:");
        
        if (($results['database']['slow_queries'] ?? 0) > 5) {
            $this->line("• 🔥 HIGH: Optimize slow database queries");
        }
        
        if (($results['database']['n_plus_one_issues'] ?? 0) > 0) {
            $this->line("• 🔥 HIGH: Fix N+1 query issues with eager loading");
        }
        
        if (($results['cache']['hit_rate'] ?? 100) < 80) {
            $this->line("• 💡 MEDIUM: Improve cache strategy and hit rate");
        }
        
        if (($results['api']['slow_endpoints'] ?? 0) > 10) {
            $this->line("• 💡 MEDIUM: Optimize slow API endpoints");
        }

        if ($output === 'json') {
            $this->newLine();
            $this->line("📄 JSON Output:");
            $this->line(json_encode($results, JSON_PRETTY_PRINT));
        }

        if ($output === 'log') {
            Log::info('Performance Analysis Results', $results);
            $this->info("📋 Results logged to application log");
        }

        $this->newLine();
        $this->info("✅ Performance analysis completed successfully!");
        $this->comment("Run with --detailed flag for more information");
        $this->comment("Use --output=json for machine-readable format");
    }

    private function calculateOverallScore($results)
    {
        $score = 100;

        // Database performance impact
        $slowQueries = $results['database']['slow_queries'] ?? 0;
        $nPlusOne = $results['database']['n_plus_one_issues'] ?? 0;
        
        $score -= min($slowQueries * 2, 20); // Max -20 for slow queries
        $score -= min($nPlusOne * 5, 25);    // Max -25 for N+1 issues

        // Cache performance impact
        $hitRate = $results['cache']['hit_rate'] ?? 100;
        if ($hitRate < 90) {
            $score -= (90 - $hitRate) * 0.5; // -0.5 per percent below 90%
        }

        // API performance impact
        $slowEndpoints = $results['api']['slow_endpoints'] ?? 0;
        $avgResponseTime = $results['api']['avg_response_time'] ?? 0;
        
        $score -= min($slowEndpoints * 1.5, 15); // Max -15 for slow endpoints
        if ($avgResponseTime > 300) {
            $score -= min(($avgResponseTime - 300) / 20, 10); // Max -10 for response time
        }

        return max(0, min(100, round($score)));
    }
}
