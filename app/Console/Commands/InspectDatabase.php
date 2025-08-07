<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class InspectDatabase extends Command
{
    protected $signature = 'db:inspect {--generate-models : Generate model files}';
    protected $description = 'Inspect existing database tables and generate model configurations';

    public function handle()
    {
        $tables = $this->getTables();
        
        $this->info("Found " . count($tables) . " tables in database:");
        
        foreach ($tables as $table) {
            $this->line("- {$table}");
        }
        
        if ($this->option('generate-models')) {
            $this->generateModels($tables);
        }
        
        return 0;
    }
    
    private function getTables(): array
    {
        $tables = [];
        $dbName = config('database.connections.pgsql.database');
        $schema = config('database.connections.pgsql.schema', 'public');
        
        $result = DB::select("
            SELECT table_name 
            FROM information_schema.tables 
            WHERE table_catalog = ? AND table_schema = ? AND table_type = 'BASE TABLE'
            ORDER BY table_name
        ", [$dbName, $schema]);
        
        foreach ($result as $row) {
            $tables[] = $row->table_name;
        }
        
        return $tables;
    }
    
    private function generateModels(array $tables): void
    {
        foreach ($tables as $table) {
            if (in_array($table, ['migrations', 'password_resets', 'failed_jobs'])) {
                continue; // Skip Laravel system tables
            }
            
            $this->generateModel($table);
        }
    }
    
    private function generateModel(string $table): void
    {
        $modelName = Str::studly(Str::singular($table));
        $columns = $this->getTableColumns($table);
        $primaryKeys = $this->getPrimaryKeys($table);
        
        $modelContent = $this->buildModelContent($modelName, $table, $columns, $primaryKeys);
        
        $filePath = app_path("Models/{$modelName}.php");
        
        if (!file_exists($filePath)) {
            file_put_contents($filePath, $modelContent);
            $this->info("Generated model: {$modelName}");
        } else {
            $this->warn("Model {$modelName} already exists, skipping...");
        }
    }
    
    private function getTableColumns(string $table): array
    {
        $schema = config('database.connections.pgsql.schema', 'public');
        
        return DB::select("
            SELECT column_name, data_type, is_nullable, column_default
            FROM information_schema.columns 
            WHERE table_schema = ? AND table_name = ?
            ORDER BY ordinal_position
        ", [$schema, $table]);
    }
    
    private function getPrimaryKeys(string $table): array
    {
        $schema = config('database.connections.pgsql.schema', 'public');
        
        $result = DB::select("
            SELECT column_name
            FROM information_schema.key_column_usage kcu
            JOIN information_schema.table_constraints tc 
                ON kcu.constraint_name = tc.constraint_name
            WHERE tc.table_schema = ? 
                AND tc.table_name = ? 
                AND tc.constraint_type = 'PRIMARY KEY'
            ORDER BY kcu.ordinal_position
        ", [$schema, $table]);
        
        return array_column($result, 'column_name');
    }
    
    private function buildModelContent(string $modelName, string $table, array $columns, array $primaryKeys): string
    {
        $hasTimestamps = $this->hasTimestampColumns($columns);
        $incrementing = $this->isAutoIncrementing($primaryKeys, $columns);
        $primaryKey = $this->formatPrimaryKey($primaryKeys);
        $fillable = $this->getFillableColumns($columns, $primaryKeys);
        
        return "<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class {$modelName} extends Model
{
    protected \$table = '{$table}';
    
    {$primaryKey}
    
    public \$incrementing = " . ($incrementing ? 'true' : 'false') . ";
    
    public \$timestamps = " . ($hasTimestamps ? 'true' : 'false') . ";
    
    protected \$fillable = [
        " . implode(",\n        ", array_map(fn($col) => "'{$col}'", $fillable)) . "
    ];
    
    // Add relationships here as needed
}
";
    }
    
    private function hasTimestampColumns(array $columns): bool
    {
        $columnNames = array_column($columns, 'column_name');
        return in_array('created_at', $columnNames) && in_array('updated_at', $columnNames);
    }
    
    private function isAutoIncrementing(array $primaryKeys, array $columns): bool
    {
        if (count($primaryKeys) !== 1) {
            return false; // Composite keys are not auto-incrementing
        }
        
        $primaryColumn = $primaryKeys[0];
        foreach ($columns as $column) {
            if ($column->column_name === $primaryColumn) {
                return str_contains($column->column_default ?? '', 'nextval') ||
                       str_contains($column->data_type, 'serial');
            }
        }
        
        return false;
    }
    
    private function formatPrimaryKey(array $primaryKeys): string
    {
        if (count($primaryKeys) === 1) {
            return "protected \$primaryKey = '{$primaryKeys[0]}';";
        } elseif (count($primaryKeys) > 1) {
            $keys = implode("', '", $primaryKeys);
            return "protected \$primaryKey = ['{$keys}'];";
        }
        
        return "// No primary key found";
    }
    
    private function getFillableColumns(array $columns, array $primaryKeys): array
    {
        $fillable = [];
        $excludeColumns = ['created_at', 'updated_at', 'deleted_at'];
        
        foreach ($columns as $column) {
            if (!in_array($column->column_name, $excludeColumns) && 
                !in_array($column->column_name, $primaryKeys)) {
                $fillable[] = $column->column_name;
            }
        }
        
        return $fillable;
    }
}
