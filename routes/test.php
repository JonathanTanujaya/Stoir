<?php

use Illuminate\Support\Facades\Route;

Route::get('/test-basic', function () {
    return response()->json([
        'success' => true,
        'message' => 'Basic test endpoint working',
        'time' => now()
    ]);
});

Route::get('/test-db', function () {
    try {
        $count = DB::table('dbo.kartustok')->count();
        return response()->json([
            'success' => true,
            'message' => 'Database connection working',
            'kartustok_count' => $count
        ]);
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ], 500);
    }
});

Route::get('/test-kartustok-raw', function () {
    try {
        $data = DB::table('dbo.kartustok')->limit(3)->get();
        return response()->json([
            'success' => true,
            'message' => 'Raw kartustok data',
            'data' => $data,
            'count' => $data->count()
        ]);
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ], 500);
    }
});
