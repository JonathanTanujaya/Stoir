<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// Test endpoint
Route::get('/test', function () {
    try {
        $user = \App\Models\MasterUser::where('kodedivisi', '01')->where('username', 'admin')->first();
        
        if ($user) {
            return response()->json([
                'status' => 'success',
                'message' => 'User found',
                'data' => [
                    'kodedivisi' => $user->kodedivisi,
                    'username' => $user->username,
                    'nama' => $user->nama,
                    'password_test' => $user->verifyPassword('admin123') ? 'OK' : 'FAIL'
                ]
            ]);
        } else {
            $allUsers = \App\Models\MasterUser::all(['kodedivisi', 'username', 'nama']);
            return response()->json([
                'status' => 'error',
                'message' => 'User not found',
                'all_users' => $allUsers
            ]);
        }
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Exception: ' . $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
});

// Authentication routes
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register'])->middleware('auth:sanctum');
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::get('/me', [AuthController::class, 'me'])->middleware('auth:sanctum');
    Route::post('/change-password', [AuthController::class, 'changePassword'])->middleware('auth:sanctum');
});
