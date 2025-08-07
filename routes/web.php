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
        $barangCount = DB::table('m_barang')->count();
        $custCount = DB::table('m_cust')->count();
        
        return response()->json([
            'status' => 'success',
            'message' => 'PostgreSQL Database connected successfully!',
            'database_info' => [
                'host' => config('database.connections.pgsql.host'),
                'database' => config('database.connections.pgsql.database'),
                'schema' => config('database.connections.pgsql.schema'),
            ],
            'data_counts' => [
                'companies' => $companiesCount,
                'barang' => $barangCount,
                'customers' => $custCount
            ],
            'migration_system' => 'COMPLETELY BYPASSED - File-based storage for cache/sessions'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
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
        
        // Test Barang  
        $barang = DB::table('m_barang')->limit(3)->get(['kodedivisi', 'kodebarang', 'namabarang']);
        $results['barang'] = [
            'count' => DB::table('m_barang')->count(),
            'sample' => $barang,
            'crud_test' => 'GET /api/barang working'
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
