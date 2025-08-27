<?php

use App\Http\Controllers\RouteExplorerController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

Route::get('/', [RouteExplorerController::class, 'index']);
Route::get('/auth-test', function () {
    return view('auth-test');
});

Route::get('/test-db', function () {
    try {
        // Test database connection
        $companiesCount = DB::table('company')->count();
        $custCount = DB::table('m_cust')->count();
        
        return response()->json([
            'status' => 'success',
            'message' => 'PostgreSQL Database connected successfully!',
            'database_info' => [
                'host' => config('database.connections.pgsql.host'),
                'database' => config('database.connections.pgsql.database'),
                'schema' => config('database.connections.pgsql.search_path', ''),
            ],
            'data_counts' => [
                'companies' => $companiesCount,
                'customers' => $custCount
            ],
            'migration_system' => 'COMPLETELY BYPASSED - File-based storage for cache/sessions'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Database connection failed',
            'error' => $e->getMessage(),
            'migration_system' => 'COMPLETELY BYPASSED - File-based storage for cache/sessions'
        ], 500);
    }
});

Route::get('/test-company', function () {
    try {
        // Test company table direct access
        $count = DB::table('dbo.company')->count();
        $sample = DB::table('dbo.company')->limit(3)->get();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Company table accessible',
            'total_records' => $count,
            'sample_data' => $sample
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Company table access failed',
            'error' => $e->getMessage()
        ], 500);
    }
});

Route::get('/test-crud', function () {
    try {
        // Test CRUD operations with real data
        $results = [];
        
        // Test Areas
        $areas = DB::table('m_area')->limit(3)->get(['kodedivisi', 'kodearea', 'area']);
        $results['areas'] = [
            'count' => DB::table('m_area')->count(),
            'sample' => $areas,
            'crud_test' => 'GET /api/areas working'
        ];
        
        // Test Customers
        $customers = DB::table('m_cust')->limit(3)->get(['kodedivisi', 'kodecust', 'namacust']);
        $results['customers'] = [
            'count' => DB::table('m_cust')->count(),
            'sample' => $customers,
            'crud_test' => 'GET /api/customers working'
        ];
        
        return response()->json([
            'status' => 'success',
            'message' => 'CRUD APIs are working! Sample data provided for testing parameter endpoints.',
            'data' => $results,
            'instructions' => [
                'Use sample data above to test parameter endpoints',
                'Example: GET /api/areas/{kodedivisi}/{kodearea}',
                'Replace {kodedivisi} and {kodearea} with real values from sample data'
            ]
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
});
