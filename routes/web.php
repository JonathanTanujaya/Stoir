<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Add login route to prevent authentication middleware errors
Route::get('/login', function () {
    return response()->json(['message' => 'Please authenticate via API token'], 401);
})->name('login');
