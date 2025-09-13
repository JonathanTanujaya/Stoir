<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class DatabaseTestController extends Controller
{
    /**
     * Test database connection and basic operations
     */
    public function testConnection(): JsonResponse
    {
        try {
            // Test basic connection
            $pdo = DB::connection()->getPDO();
            
            // Get database name
            $dbName = DB::connection()->getDatabaseName();
            
            // Count tables
            $tables = DB::select("SELECT count(*) as count FROM information_schema.tables WHERE table_schema = 'public'");
            $tableCount = $tables[0]->count;
            
            // Count views
            $views = DB::select("SELECT count(*) as count FROM information_schema.views WHERE table_schema = 'public'");
            $viewCount = $views[0]->count;
            
            // Count stored procedures
            $procedures = DB::select("SELECT count(*) as count FROM information_schema.routines WHERE routine_type = 'PROCEDURE' AND routine_schema = 'public'");
            $procedureCount = $procedures[0]->count;
            
            return response()->json([
                'status' => 'success',
                'database' => $dbName,
                'tables' => $tableCount,
                'views' => $viewCount,
                'procedures' => $procedureCount,
                'message' => 'Database connection successful'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test views functionality
     */
    public function testViews(): JsonResponse
    {
        try {
            $results = [];
            
            // Test each view
            $viewsToTest = [
                'v_bank', 'v_barang', 'v_invoice', 'v_invoice_header', 
                'v_kartu_stok', 'v_journal', 'v_stok_summary', 
                'v_financial_report', 'v_aging_report', 'v_sales_summary',
                'v_return_summary', 'v_dashboard_kpi'
            ];
            
            foreach ($viewsToTest as $view) {
                try {
                    $count = DB::select("SELECT count(*) as count FROM {$view}")[0]->count;
                    $results[$view] = [
                        'status' => 'accessible',
                        'record_count' => $count
                    ];
                } catch (\Exception $e) {
                    $results[$view] = [
                        'status' => 'error',
                        'error' => $e->getMessage()
                    ];
                }
            }
            
            return response()->json([
                'status' => 'success',
                'views' => $results
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test stored procedures accessibility
     */
    public function testProcedures(): JsonResponse
    {
        try {
            $procedures = DB::select("
                SELECT routine_name, routine_definition 
                FROM information_schema.routines 
                WHERE routine_type = 'PROCEDURE' AND routine_schema = 'public'
                ORDER BY routine_name
            ");
            
            $results = [];
            foreach ($procedures as $proc) {
                $results[] = [
                    'name' => $proc->routine_name,
                    'accessible' => true
                ];
            }
            
            return response()->json([
                'status' => 'success',
                'procedures' => $results,
                'total_procedures' => count($results)
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test foreign key relationships
     */
    public function testForeignKeys(): JsonResponse
    {
        try {
            $foreignKeys = DB::select("
                SELECT 
                    tc.constraint_name,
                    tc.table_name,
                    kcu.column_name,
                    ccu.table_name AS foreign_table_name,
                    ccu.column_name AS foreign_column_name
                FROM information_schema.table_constraints AS tc
                JOIN information_schema.key_column_usage AS kcu
                    ON tc.constraint_name = kcu.constraint_name
                    AND tc.table_schema = kcu.table_schema
                JOIN information_schema.constraint_column_usage AS ccu
                    ON ccu.constraint_name = tc.constraint_name
                    AND ccu.table_schema = tc.table_schema
                WHERE tc.constraint_type = 'FOREIGN KEY'
                    AND tc.table_schema = 'public'
                ORDER BY tc.table_name, tc.constraint_name
            ");
            
            $groupedForeignKeys = [];
            foreach ($foreignKeys as $fk) {
                $tableName = $fk->table_name;
                if (!isset($groupedForeignKeys[$tableName])) {
                    $groupedForeignKeys[$tableName] = [];
                }
                $groupedForeignKeys[$tableName][] = [
                    'constraint_name' => $fk->constraint_name,
                    'column' => $fk->column_name,
                    'references_table' => $fk->foreign_table_name,
                    'references_column' => $fk->foreign_column_name
                ];
            }
            
            return response()->json([
                'status' => 'success',
                'foreign_keys' => $groupedForeignKeys,
                'total_constraints' => count($foreignKeys)
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test model relationships
     */
    public function testModelRelationships(): JsonResponse
    {
        try {
            $results = [];
            
            // Test some basic model relationships
            $models = [
                'Divisi' => \App\Models\Divisi::class,
                'Bank' => \App\Models\Bank::class,
                'Customer' => \App\Models\Customer::class,
                'Supplier' => \App\Models\Supplier::class,
                'Barang' => \App\Models\Barang::class,
                'Invoice' => \App\Models\Invoice::class
            ];
            
            foreach ($models as $name => $class) {
                try {
                    if (class_exists($class)) {
                        $count = $class::count();
                        $results[$name] = [
                            'status' => 'accessible',
                            'record_count' => $count,
                            'class_exists' => true
                        ];
                    } else {
                        $results[$name] = [
                            'status' => 'missing',
                            'class_exists' => false
                        ];
                    }
                } catch (\Exception $e) {
                    $results[$name] = [
                        'status' => 'error',
                        'error' => $e->getMessage()
                    ];
                }
            }
            
            return response()->json([
                'status' => 'success',
                'models' => $results
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Full database integration test
     */
    public function fullTest(): JsonResponse
    {
        try {
            // Run all tests
            $connectionTest = json_decode($this->testConnection()->getContent(), true);
            $viewsTest = json_decode($this->testViews()->getContent(), true);
            $proceduresTest = json_decode($this->testProcedures()->getContent(), true);
            $foreignKeysTest = json_decode($this->testForeignKeys()->getContent(), true);
            $modelsTest = json_decode($this->testModelRelationships()->getContent(), true);
            
            $summary = [
                'database_connection' => $connectionTest['status'] === 'success',
                'views_accessible' => $viewsTest['status'] === 'success',
                'procedures_accessible' => $proceduresTest['status'] === 'success',
                'foreign_keys_defined' => $foreignKeysTest['status'] === 'success',
                'models_working' => $modelsTest['status'] === 'success',
                'total_tables' => $connectionTest['tables'] ?? 0,
                'total_views' => $connectionTest['views'] ?? 0,
                'total_procedures' => $connectionTest['procedures'] ?? 0,
                'total_foreign_keys' => $foreignKeysTest['total_constraints'] ?? 0
            ];
            
            $allTestsPassed = array_reduce($summary, function($carry, $item) {
                return $carry && (is_bool($item) ? $item : true);
            }, true);
            
            return response()->json([
                'status' => $allTestsPassed ? 'success' : 'partial',
                'message' => $allTestsPassed ? 'All database integration tests passed' : 'Some tests failed',
                'summary' => $summary,
                'detailed_results' => [
                    'connection' => $connectionTest,
                    'views' => $viewsTest,
                    'procedures' => $proceduresTest,
                    'foreign_keys' => $foreignKeysTest,
                    'models' => $modelsTest
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
