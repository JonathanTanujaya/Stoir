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
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\CustomersController;
use App\Http\Controllers\SuppliersController;
use App\Http\Controllers\BarangsController;
use App\Http\Controllers\MasterUserController;
use App\Http\Controllers\MBankController;
use App\Http\Controllers\MCOAController;
use App\Http\Controllers\MDivisiController;
use App\Http\Controllers\MDokumenController;
use App\Http\Controllers\UserModuleController;
use App\Http\Controllers\MResiController;
use App\Http\Controllers\MTTController;
use App\Http\Controllers\MTransController;
use App\Http\Controllers\MVoucherController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\TmpPrintInvoiceController;
use App\Http\Controllers\KartuStokController;

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
// Return/Retur controllers
use App\Http\Controllers\ReturPembelianController;
use App\Http\Controllers\ReturPenjualanController;

// ===========================
// AUTHENTICATION ROUTES (PUBLIC FOR DEVELOPMENT)
// ===========================
Route::post('auth/login', [AuthController::class, 'login']);
Route::post('auth/register', [AuthController::class, 'register']); // Removed middleware for development
Route::post('auth/logout', [AuthController::class, 'logout']); // Made public for development
Route::get('auth/me', [AuthController::class, 'me']); // Made public for development
Route::post('auth/change-password', [AuthController::class, 'changePassword']); // Made public for development

// ===========================
// PUBLIC ROUTES FOR DEVELOPMENT
// ===========================
Route::get('categories', [CategoriesController::class, 'index']);
Route::post('categories', [CategoriesController::class, 'store']);
Route::get('categories/{id}', [CategoriesController::class, 'show']);
Route::put('categories/{id}', [CategoriesController::class, 'update']);
Route::delete('categories/{id}', [CategoriesController::class, 'destroy']);

Route::get('customers', [CustomersController::class, 'index']);
Route::post('customers', [CustomersController::class, 'store']);
Route::get('customers/{id}', [CustomersController::class, 'show']);
Route::put('customers/{id}', [CustomersController::class, 'update']);
Route::delete('customers/{id}', [CustomersController::class, 'destroy']);

Route::get('suppliers', [SuppliersController::class, 'index']);
Route::post('suppliers', [SuppliersController::class, 'store']);
Route::get('suppliers/{id}', [SuppliersController::class, 'show']);
Route::put('suppliers/{id}', [SuppliersController::class, 'update']);
Route::delete('suppliers/{id}', [SuppliersController::class, 'destroy']);

Route::get('barang', [BarangsController::class, 'index']);
Route::post('barang', [BarangsController::class, 'store']);
Route::get('barang/{id}', [BarangsController::class, 'show']);
Route::put('barang/{id}', [BarangsController::class, 'update']);
Route::delete('barang/{id}', [BarangsController::class, 'destroy']);

Route::get('invoices', [InvoiceController::class, 'index']);
Route::get('sales', [SalesController::class, 'index']);

// ===========================
// USER ROUTES (PUBLIC FOR DEVELOPMENT)
// ===========================
Route::get('/user', function (Request $request) {
    // Return dummy user for development
    return response()->json([
        'id' => 1,
        'name' => 'Development User',
        'email' => 'dev@stockflow.com',
        'role' => 'admin'
    ]);
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
Route::get('barang/{kodeDivisi}', [BarangController::class, 'showByDivisi']); 
Route::get('barang/{kodeDivisi}/{kodeBarang}', [BarangController::class, 'show']);
Route::put('barang/{kodeDivisi}/{kodeBarang}', [BarangController::class, 'update']);
Route::delete('barang/{kodeDivisi}/{kodeBarang}', [BarangController::class, 'destroy']);

// Customer routes with composite keys - COMMENTED OUT DUE TO ROUTE CONFLICT
// Route::get('customers', [CustomerController::class, 'index']);
// Route::post('customers', [CustomerController::class, 'store']);
// Route::get('customers/{kodeDivisi}', [CustomerController::class, 'showByDivisi']); 
// Route::get('customers/{kodeDivisi}/{kodeCust}', [CustomerController::class, 'show']);
// Route::put('customers/{kodeDivisi}/{kodeCust}', [CustomerController::class, 'update']);
// Route::delete('customers/{kodeDivisi}/{kodeCust}', [CustomerController::class, 'destroy']);

// Sales routes with composite keys
Route::get('sales', [SalesController::class, 'index']);
Route::post('sales', [SalesController::class, 'store']);
Route::get('sales/{kodeDivisi}', [SalesController::class, 'showByDivisi']); 
Route::get('sales/{kodeDivisi}/{kodeSales}', [SalesController::class, 'show']);
Route::put('sales/{kodeDivisi}/{kodeSales}', [SalesController::class, 'update']);
Route::delete('sales/{kodeDivisi}/{kodeSales}', [SalesController::class, 'destroy']);

// Sales Transaction/Form specific routes
// Route::get('customers', [InvoiceController::class, 'getCustomers']); // COMMENTED OUT DUE TO ROUTE CONFLICT
Route::get('sales-persons', [InvoiceController::class, 'getSalesPersons']);
Route::get('barang', [InvoiceController::class, 'getBarang']);
Route::get('sales/transactions', [InvoiceController::class, 'index']);
Route::post('sales/transactions', [InvoiceController::class, 'store']);
Route::get('sales/transactions/{id}', [InvoiceController::class, 'show']);

// Supplier routes with composite keys
Route::get('suppliers', [SupplierController::class, 'index']);
Route::post('suppliers', [SupplierController::class, 'store']);
Route::get('suppliers/{kodeDivisi}', [SupplierController::class, 'showByDivisi']); 
Route::get('suppliers/{kodeDivisi}/{kodeSupplier}', [SupplierController::class, 'show']);
Route::put('suppliers/{kodeDivisi}/{kodeSupplier}', [SupplierController::class, 'update']);
Route::delete('suppliers/{kodeDivisi}/{kodeSupplier}', [SupplierController::class, 'destroy']);

// Kategori routes
Route::get('kategoris', [KategoriController::class, 'index']);
// Route::get('categories', [KategoriController::class, 'index']); // Alias - DISABLED, use CategoriesController instead
Route::post('kategoris', [KategoriController::class, 'store']);
Route::get('kategoris/{kodeDivisi}', [KategoriController::class, 'showByDivisi']); 
Route::get('kategoris/{kodeDivisi}/{kodeKategori}', [KategoriController::class, 'show']);
Route::put('kategoris/{kodeDivisi}/{kodeKategori}', [KategoriController::class, 'update']);
Route::delete('kategoris/{kodeDivisi}/{kodeKategori}', [KategoriController::class, 'destroy']);

// Master User routes
Route::get('master-users', [MasterUserController::class, 'index']);
Route::get('users', [MasterUserController::class, 'index']); // Alias
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
Route::get('divisions', [MDivisiController::class, 'index']); // Alias
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

// Part Penerimaan routes
Route::get('part-penerimaan', [PartPenerimaanController::class, 'index']); // Limited untuk testing Laravel
Route::get('part-penerimaan/all', [PartPenerimaanController::class, 'getAllForFrontend']); // Semua data untuk frontend
Route::post('part-penerimaan', [PartPenerimaanController::class, 'store']);
Route::get('part-penerimaan/{id}', [PartPenerimaanController::class, 'show']);
Route::put('part-penerimaan/{id}', [PartPenerimaanController::class, 'update']);
Route::delete('part-penerimaan/{id}', [PartPenerimaanController::class, 'destroy']);

// Purchases routes (Modern API)
Route::get('purchases', [PurchasesController::class, 'index']);
Route::post('purchases', [PurchasesController::class, 'store']);
Route::get('purchases/check-invoice', [PurchasesController::class, 'checkInvoice']);
Route::get('purchases/{id}', [PurchasesController::class, 'show']);
Route::delete('purchases/{id}', [PurchasesController::class, 'destroy']);

// Return Pembelian routes
Route::get('return-purchases', [ReturPembelianController::class, 'index']);
Route::post('return-purchases', [ReturPembelianController::class, 'store']);
Route::get('return-purchases/purchases', [ReturPembelianController::class, 'getPurchases']);
Route::get('return-purchases/purchase-details/{purchaseId}', [ReturPembelianController::class, 'getPurchaseDetails']);

// Return Penjualan routes
Route::get('return-sales', [ReturPenjualanController::class, 'index']);
Route::post('return-sales', [ReturPenjualanController::class, 'store']);
Route::get('return-sales/invoices', [ReturPenjualanController::class, 'getInvoices']);
Route::get('return-sales/invoice-details/{invoiceNumber}', [ReturPenjualanController::class, 'getInvoiceDetails']);
Route::get('return-sales/customers', [ReturPenjualanController::class, 'getCustomers']);

// Penerimaan Finance routes
Route::get('penerimaan-finance', [PenerimaanFinanceController::class, 'index']); // Limited untuk testing Laravel
Route::get('penerimaan-finance/all', [PenerimaanFinanceController::class, 'getAllForFrontend']); // Semua data untuk frontend
Route::post('penerimaan-finance', [PenerimaanFinanceController::class, 'store']);
Route::get('penerimaan-finance/{id}', [PenerimaanFinanceController::class, 'show']);
Route::put('penerimaan-finance/{id}', [PenerimaanFinanceController::class, 'update']);
Route::delete('penerimaan-finance/{id}', [PenerimaanFinanceController::class, 'destroy']);

// ===========================
// JOURNAL & REPORTING ROUTES
// ===========================

// Journal routes
Route::get('journals', [JournalController::class, 'index']); // Limited untuk testing Laravel
Route::get('journals/all', [JournalController::class, 'getAllForFrontend']); // Semua data untuk frontend
Route::post('journals', [JournalController::class, 'store']);
Route::get('journals/{id}', [JournalController::class, 'show']);
Route::put('journals/{id}', [JournalController::class, 'update']);
Route::delete('journals/{id}', [JournalController::class, 'destroy']);

// Kartu Stok routes
Route::get('kartu-stok', [KartuStokController::class, 'index']); // Limited untuk testing Laravel
Route::get('kartu-stok/all', [KartuStokController::class, 'getAllForFrontend']); // Semua data untuk frontend
Route::post('kartu-stok', [KartuStokController::class, 'store']);
Route::get('kartu-stok/{id}', [KartuStokController::class, 'show']);
Route::put('kartu-stok/{id}', [KartuStokController::class, 'update']);
Route::delete('kartu-stok/{id}', [KartuStokController::class, 'destroy']);
Route::get('kartu-stok/by-barang/{kodeDivisi}/{kodeBarang}', [KartuStokController::class, 'getByBarang']);

// Company routes
Route::get('companies', [CompanyController::class, 'index']);
Route::post('companies', [CompanyController::class, 'store']);
Route::get('companies/{id}', [CompanyController::class, 'show']);
Route::put('companies/{id}', [CompanyController::class, 'update']);
Route::delete('companies/{id}', [CompanyController::class, 'destroy']);

// Tmp Print Invoice routes
Route::get('tmp-print-invoices', [TmpPrintInvoiceController::class, 'index']);
Route::post('tmp-print-invoices', [TmpPrintInvoiceController::class, 'store']);
Route::get('tmp-print-invoices/{id}', [TmpPrintInvoiceController::class, 'show']);
Route::put('tmp-print-invoices/{id}', [TmpPrintInvoiceController::class, 'update']);
Route::delete('tmp-print-invoices/{id}', [TmpPrintInvoiceController::class, 'destroy']);

// Opname routes
Route::get('opnames', [OpnameController::class, 'index']);
Route::post('opnames', [OpnameController::class, 'store']);
Route::get('opnames/{id}', [OpnameController::class, 'show']);
Route::put('opnames/{id}', [OpnameController::class, 'update']);
Route::delete('opnames/{id}', [OpnameController::class, 'destroy']);

// Dokumen routes
Route::get('dokumens', [MDokumenController::class, 'index']);
Route::get('documents', [MDokumenController::class, 'index']); // Alias
Route::post('dokumens', [MDokumenController::class, 'store']);
Route::get('dokumens/{kodeDivisi}', [MDokumenController::class, 'showByDivisi']); 
Route::get('dokumens/{kodeDivisi}/{kodeDokumen}', [MDokumenController::class, 'show']);
Route::put('dokumens/{kodeDivisi}/{kodeDokumen}', [MDokumenController::class, 'update']);
Route::delete('dokumens/{kodeDivisi}/{kodeDokumen}', [MDokumenController::class, 'destroy']);

// Module routes
Route::get('user-modules', [UserModuleController::class, 'index']);
Route::get('modules', [UserModuleController::class, 'index']); // Alias
Route::post('user-modules', [UserModuleController::class, 'store']);
Route::get('user-modules/{kodeDivisi}', [UserModuleController::class, 'showByDivisi']); 
Route::get('user-modules/{kodeDivisi}/{kodeModule}', [UserModuleController::class, 'show']);
Route::put('user-modules/{kodeDivisi}/{kodeModule}', [UserModuleController::class, 'update']);
Route::delete('user-modules/{kodeDivisi}/{kodeModule}', [UserModuleController::class, 'destroy']);

// Return Sales routes - handled by ReturPenjualanController above

// Invoice Details routes (assume handled by InvoiceController)
Route::get('invoice-details', [InvoiceController::class, 'getAllDetails']);
Route::get('invoices/{invoiceId}/details', [InvoiceController::class, 'getDetails']);
