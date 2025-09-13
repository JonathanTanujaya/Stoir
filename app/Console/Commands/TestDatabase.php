<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TestDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:database {--full : Run full comprehensive test}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test database integration including tables, views, procedures, and foreign keys';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🔍 Testing Database Integration...');
        $this->newLine();

        // Test 1: Database Connection
        $this->testConnection();
        
        // Test 2: Tables and Views
        $this->testTablesAndViews();
        
        // Test 3: Stored Procedures
        $this->testStoredProcedures();
        
        // Test 4: Foreign Keys
        $this->testForeignKeys();
        
        // Test 5: Models (if full test)
        if ($this->option('full')) {
            $this->testModels();
        }
        
        $this->newLine();
        $this->info('✅ Database integration test completed!');
        
        return Command::SUCCESS;
    }

    private function testConnection(): void
    {
        $this->info('1. Testing Database Connection...');
        
        try {
            $pdo = DB::connection()->getPDO();
            $dbName = DB::connection()->getDatabaseName();
            $this->line("   ✅ Connected to database: {$dbName}");
        } catch (\Exception $e) {
            $this->error("   ❌ Connection failed: " . $e->getMessage());
            return;
        }
    }

    private function testTablesAndViews(): void
    {
        $this->info('2. Testing Tables and Views...');
        
        try {
            // Count tables
            $tables = DB::select("SELECT count(*) as count FROM information_schema.tables WHERE table_schema = 'public'");
            $tableCount = $tables[0]->count;
            $this->line("   ✅ Tables found: {$tableCount}");
            
            // Count views  
            $views = DB::select("SELECT count(*) as count FROM information_schema.views WHERE table_schema = 'public'");
            $viewCount = $views[0]->count;
            $this->line("   ✅ Views found: {$viewCount}");
            
            // Test some specific views
            $testViews = ['v_bank', 'v_barang', 'v_invoice', 'v_stok_summary'];
            foreach ($testViews as $view) {
                try {
                    $count = DB::select("SELECT count(*) as count FROM {$view}")[0]->count;
                    $this->line("   ✅ View {$view}: {$count} records");
                } catch (\Exception $e) {
                    $this->line("   ❌ View {$view}: Error - " . $e->getMessage());
                }
            }
            
        } catch (\Exception $e) {
            $this->error("   ❌ Tables/Views test failed: " . $e->getMessage());
        }
    }

    private function testStoredProcedures(): void
    {
        $this->info('3. Testing Stored Procedures...');
        
        try {
            $procedures = DB::select("SELECT routine_name FROM information_schema.routines WHERE routine_type = 'PROCEDURE' AND routine_schema = 'public'");
            
            $this->line("   ✅ Found " . count($procedures) . " stored procedures:");
            foreach ($procedures as $proc) {
                $this->line("      - {$proc->routine_name}");
            }
            
        } catch (\Exception $e) {
            $this->error("   ❌ Stored procedures test failed: " . $e->getMessage());
        }
    }

    private function testForeignKeys(): void
    {
        $this->info('4. Testing Foreign Key Constraints...');
        
        try {
            $foreignKeys = DB::select("
                SELECT 
                    tc.table_name,
                    kcu.column_name,
                    ccu.table_name AS foreign_table_name,
                    ccu.column_name AS foreign_column_name
                FROM information_schema.table_constraints AS tc
                JOIN information_schema.key_column_usage AS kcu
                    ON tc.constraint_name = kcu.constraint_name
                JOIN information_schema.constraint_column_usage AS ccu
                    ON ccu.constraint_name = tc.constraint_name
                WHERE tc.constraint_type = 'FOREIGN KEY'
                    AND tc.table_schema = 'public'
                ORDER BY tc.table_name
            ");
            
            $this->line("   ✅ Found " . count($foreignKeys) . " foreign key constraints");
            
            // Group by table
            $byTable = [];
            foreach ($foreignKeys as $fk) {
                $byTable[$fk->table_name][] = $fk;
            }
            
            foreach ($byTable as $table => $fks) {
                $this->line("      {$table}: " . count($fks) . " foreign keys");
            }
            
        } catch (\Exception $e) {
            $this->error("   ❌ Foreign keys test failed: " . $e->getMessage());
        }
    }

    private function testModels(): void
    {
        $this->info('5. Testing Laravel Models...');
        
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
                    $this->line("   ✅ Model {$name}: {$count} records");
                } else {
                    $this->line("   ❌ Model {$name}: Class not found");
                }
            } catch (\Exception $e) {
                $this->line("   ❌ Model {$name}: " . $e->getMessage());
            }
        }
    }
}
