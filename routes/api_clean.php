<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Authentication Controller
use App\Http\Controllers\AuthController;

// Master Data Controllers
use App\Http\Controllers\AreaController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\MasterUserController;
use App\Http\Controllers\MBankController;
use App\Http\Controllers\MCOAController;
use App\Http\Controllers\MDivisiController;
use App\Http\Controllers\MDokumenController;
use App\Http\Controllers\MResiController;
use App\Http\Controllers\MTTController;
use App\Http\Controllers\MTransController;
use App\Http\Controllers\MVoucherController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\SupplierController;

// Transaction Controllers
use App\Http\Controllers\ClaimController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\JournalController;
use App\Http\Controllers\PartPenerimaanController;
use App\Http\Controllers\PartPenerimaanBonusController;
use App\Http\Controllers\PenerimaanFinanceController;
use App\Http\Controllers\ReturnSalesController;
use App\Http\Controllers\ReturPenerimaanController;
use App\Http\Controllers\SPVController;
use App\Http\Controllers\StokClaimController;
use App\Http\Controllers\OpnameController;
use App\Http\Controllers\MergeBarangController;

// ===========================
// AUTHENTICATION ROUTES
// ===========================
Route::post('auth/login', [AuthController::class, 'login']);
Route::post('auth/register', [AuthController::class, 'register'])->middleware(['auth:sanctum', 'admin']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('auth/logout', [AuthController::class, 'logout']);
    Route::get('auth/me', [AuthController::class, 'me']);
    Route::post('auth/change-password', [AuthController::class, 'changePassword']);
});

// ===========================
// PROTECTED ROUTES
// ===========================
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// ===========================
// MASTER DATA ROUTES
// ===========================

// Area routes with composite keys
Route::get('areas', [AreaController::class, 'index']);
Route::post('areas', [AreaController::class, 'store']);
Route::get('areas/{kodeDivisi}', [AreaController::class, 'showByDivisi']); 
Route::get('areas/{kodeDivisi}/{kodeArea}', [AreaController::class, 'show']);
Route::put('areas/{kodeDivisi}/{kodeArea}', [AreaController::class, 'update']);
Route::delete('areas/{kodeDivisi}/{kodeArea}', [AreaController::class, 'destroy']);

// Barang routes with composite keys
Route::get('barang', [BarangController::class, 'index']);
Route::post('barang', [BarangController::class, 'store']);
Route::get('barang/{kodeDivisi}', [BarangController::class, 'showByDivisi']); 
Route::get('barang/{kodeDivisi}/{kodeBarang}', [BarangController::class, 'show']);
Route::put('barang/{kodeDivisi}/{kodeBarang}', [BarangController::class, 'update']);
Route::delete('barang/{kodeDivisi}/{kodeBarang}', [BarangController::class, 'destroy']);

// Customer routes with composite keys
Route::get('customers', [CustomerController::class, 'index']);
Route::post('customers', [CustomerController::class, 'store']);
Route::get('customers/{kodeDivisi}', [CustomerController::class, 'showByDivisi']); 
Route::get('customers/{kodeDivisi}/{kodeCust}', [CustomerController::class, 'show']);
Route::put('customers/{kodeDivisi}/{kodeCust}', [CustomerController::class, 'update']);
Route::delete('customers/{kodeDivisi}/{kodeCust}', [CustomerController::class, 'destroy']);

// Sales routes with composite keys
Route::get('sales', [SalesController::class, 'index']);
Route::post('sales', [SalesController::class, 'store']);
Route::get('sales/{kodeDivisi}', [SalesController::class, 'showByDivisi']); 
Route::get('sales/{kodeDivisi}/{kodeSales}', [SalesController::class, 'show']);
Route::put('sales/{kodeDivisi}/{kodeSales}', [SalesController::class, 'update']);
Route::delete('sales/{kodeDivisi}/{kodeSales}', [SalesController::class, 'destroy']);

// Supplier routes with composite keys
Route::get('suppliers', [SupplierController::class, 'index']);
Route::post('suppliers', [SupplierController::class, 'store']);
Route::get('suppliers/{kodeDivisi}', [SupplierController::class, 'showByDivisi']); 
Route::get('suppliers/{kodeDivisi}/{kodeSupplier}', [SupplierController::class, 'show']);
Route::put('suppliers/{kodeDivisi}/{kodeSupplier}', [SupplierController::class, 'update']);
Route::delete('suppliers/{kodeDivisi}/{kodeSupplier}', [SupplierController::class, 'destroy']);

// Kategori routes
Route::get('kategoris', [KategoriController::class, 'index']);
Route::post('kategoris', [KategoriController::class, 'store']);
Route::get('kategoris/{kodeDivisi}', [KategoriController::class, 'showByDivisi']); 
Route::get('kategoris/{kodeDivisi}/{kodeKategori}', [KategoriController::class, 'show']);
Route::put('kategoris/{kodeDivisi}/{kodeKategori}', [KategoriController::class, 'update']);
Route::delete('kategoris/{kodeDivisi}/{kodeKategori}', [KategoriController::class, 'destroy']);

// Master User routes
Route::get('master-users', [MasterUserController::class, 'index']);
Route::post('master-users', [MasterUserController::class, 'store']);
Route::get('master-users/{kodeDivisi}', [MasterUserController::class, 'showByDivisi']); 
Route::get('master-users/{kodeDivisi}/{username}', [MasterUserController::class, 'show']);
Route::put('master-users/{kodeDivisi}/{username}', [MasterUserController::class, 'update']);
Route::delete('master-users/{kodeDivisi}/{username}', [MasterUserController::class, 'destroy']);

// Bank routes
Route::get('banks', [MBankController::class, 'index']);
Route::post('banks', [MBankController::class, 'store']);
Route::get('banks/{kodeDivisi}', [MBankController::class, 'showByDivisi']); 
Route::get('banks/{kodeDivisi}/{kodeBank}', [MBankController::class, 'show']);
Route::put('banks/{kodeDivisi}/{kodeBank}', [MBankController::class, 'update']);
Route::delete('banks/{kodeDivisi}/{kodeBank}', [MBankController::class, 'destroy']);

// COA routes
Route::get('coas', [MCOAController::class, 'index']);
Route::post('coas', [MCOAController::class, 'store']);
Route::get('coas/{kodeDivisi}', [MCOAController::class, 'showByDivisi']); 
Route::get('coas/{kodeDivisi}/{kodeCOA}', [MCOAController::class, 'show']);
Route::put('coas/{kodeDivisi}/{kodeCOA}', [MCOAController::class, 'update']);
Route::delete('coas/{kodeDivisi}/{kodeCOA}', [MCOAController::class, 'destroy']);

// Divisi routes
Route::get('divisis', [MDivisiController::class, 'index']);
Route::post('divisis', [MDivisiController::class, 'store']);
Route::get('divisis/{kodeDivisi}', [MDivisiController::class, 'show']);
Route::put('divisis/{kodeDivisi}', [MDivisiController::class, 'update']);
Route::delete('divisis/{kodeDivisi}', [MDivisiController::class, 'destroy']);

// ===========================
// TRANSACTION ROUTES
// ===========================

// Invoice routes
Route::get('invoices', [InvoiceController::class, 'index']);
Route::post('invoices', [InvoiceController::class, 'store']);
Route::get('invoices/{id}', [InvoiceController::class, 'show']);
Route::put('invoices/{id}', [InvoiceController::class, 'update']);
Route::delete('invoices/{id}', [InvoiceController::class, 'destroy']);

// Invoice Bonus routes

// Part Penerimaan routes
Route::get('part-penerimaan', [PartPenerimaanController::class, 'index']);
Route::post('part-penerimaan', [PartPenerimaanController::class, 'store']);
Route::get('part-penerimaan/{id}', [PartPenerimaanController::class, 'show']);
Route::put('part-penerimaan/{id}', [PartPenerimaanController::class, 'update']);
Route::delete('part-penerimaan/{id}', [PartPenerimaanController::class, 'destroy']);

// Penerimaan Finance routes
Route::get('penerimaan-finance', [PenerimaanFinanceController::class, 'index']);
Route::post('penerimaan-finance', [PenerimaanFinanceController::class, 'store']);
Route::get('penerimaan-finance/{id}', [PenerimaanFinanceController::class, 'show']);
Route::put('penerimaan-finance/{id}', [PenerimaanFinanceController::class, 'update']);
Route::delete('penerimaan-finance/{id}', [PenerimaanFinanceController::class, 'destroy']);

// ===========================
// JOURNAL & REPORTING ROUTES
// ===========================

// Journal routes
Route::get('journals', [JournalController::class, 'index']);
Route::post('journals', [JournalController::class, 'store']);
Route::get('journals/{id}', [JournalController::class, 'show']);
Route::put('journals/{id}', [JournalController::class, 'update']);
Route::delete('journals/{id}', [JournalController::class, 'destroy']);

// Kartu Stok routes

// Opname routes
Route::get('opnames', [OpnameController::class, 'index']);
Route::post('opnames', [OpnameController::class, 'store']);
Route::get('opnames/{id}', [OpnameController::class, 'show']);
Route::put('opnames/{id}', [OpnameController::class, 'update']);
Route::delete('opnames/{id}', [OpnameController::class, 'destroy']);
