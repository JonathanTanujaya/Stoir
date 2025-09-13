<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DivisiController;
use App\Http\Controllers\BankController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ProcedureController;
use App\Http\Controllers\DPaketController;
use App\Http\Controllers\DTtController;
use App\Http\Controllers\DVoucherController;
use App\Http\Controllers\MDokumenController;
use App\Http\Controllers\MResiController;
use App\Http\Controllers\MTtController;
use App\Http\Controllers\MVoucherController;
use App\Http\Controllers\PenerimaanFinanceController;
use App\Http\Controllers\PenerimaanFinanceDetailController;
use App\Http\Controllers\ReturPenerimaanController;
use App\Http\Controllers\ReturPenerimaanDetailController;
use App\Http\Controllers\ReturnSalesController;
use App\Http\Controllers\ReturnSalesDetailController;
use App\Http\Controllers\SaldoBankController;
use App\Http\Controllers\StokMinimumController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Procedure endpoints for business logic
Route::prefix('procedures')->group(function () {
    Route::post('/invoice', [ProcedureController::class, 'createInvoice']);
    Route::post('/part-penerimaan', [ProcedureController::class, 'createPartPenerimaan']);
    Route::post('/retur-sales', [ProcedureController::class, 'createReturSales']);
    Route::post('/batalkan-invoice', [ProcedureController::class, 'batalkanInvoice']);
    Route::post('/stok-opname', [ProcedureController::class, 'stokOpname']);
    Route::post('/generate-nomor', [ProcedureController::class, 'generateNomor']);
    Route::post('/master-resi', [ProcedureController::class, 'createMasterResi']);
    Route::post('/tambah-saldo', [ProcedureController::class, 'tambahSaldo']);
    Route::post('/tanda-terima', [ProcedureController::class, 'createTandaTerima']);
    Route::post('/voucher', [ProcedureController::class, 'createVoucher']);
    Route::post('/merge-barang', [ProcedureController::class, 'mergeBarang']);
    Route::post('/journal-invoice', [ProcedureController::class, 'journalInvoice']);
    Route::post('/journal-retur-sales', [ProcedureController::class, 'journalReturSales']);
    Route::post('/journal-penerimaan', [ProcedureController::class, 'journalPenerimaan']);
});

// Report endpoints for database views
Route::prefix('reports')->group(function () {
    Route::get('/bank', [ReportController::class, 'getBankReport']);
    Route::get('/barang', [ReportController::class, 'getBarangReport']);
    Route::get('/invoice', [ReportController::class, 'getInvoiceReport']);
    Route::get('/invoice-header', [ReportController::class, 'getInvoiceHeaderReport']);
    Route::get('/kartu-stok', [ReportController::class, 'getKartuStokReport']);
    Route::get('/part-penerimaan', [ReportController::class, 'getPartPenerimaanReport']);
    Route::get('/penerimaan-finance', [ReportController::class, 'getPenerimaanFinanceReport']);
    Route::get('/return-sales-detail', [ReportController::class, 'getReturnSalesDetailReport']);
    Route::get('/stok-summary', [ReportController::class, 'stokSummary']);
    Route::get('/financial', [ReportController::class, 'financialReport']);
    Route::get('/aging', [ReportController::class, 'agingReport']);
    Route::get('/sales-summary', [ReportController::class, 'salesSummary']);
    Route::get('/return-summary', [ReportController::class, 'returnSummary']);
    Route::get('/dashboard-kpi', [ReportController::class, 'dashboardKpi']);
    Route::get('/journal', [ReportController::class, 'getJournalReport']);
    Route::get('/tt', [ReportController::class, 'getTtReport']);
    Route::get('/voucher', [ReportController::class, 'getVoucherReport']);
});

// Divisi routes
Route::apiResource('divisi', DivisiController::class)->parameters(['divisi' => 'kodeDivisi']);

// Bank routes with nested resource
Route::prefix('divisi/{kodeDivisi}')->group(function () {
    Route::apiResource('banks', BankController::class)->parameters(['banks' => 'kodeBank']);
});

// Area routes with nested resource
Route::prefix('divisi/{kodeDivisi}')->group(function () {
    Route::apiResource('areas', AreaController::class)->parameters(['areas' => 'kodeArea']);
});

// Sales routes with nested resource
Route::prefix('divisi/{kodeDivisi}')->group(function () {
    Route::apiResource('sales', SalesController::class)->parameters(['sales' => 'kodeSales']);
});

// Customer routes with nested resource
Route::prefix('divisi/{kodeDivisi}')->group(function () {
    Route::apiResource('customers', CustomerController::class)->parameters(['customers' => 'kodeCust']);
});

// Supplier routes with nested resource
Route::prefix('divisi/{kodeDivisi}')->group(function () {
    Route::apiResource('suppliers', SupplierController::class)->parameters(['suppliers' => 'kodeSupplier']);
});

// Barang routes with nested resource
Route::prefix('divisi/{kodeDivisi}')->group(function () {
    Route::apiResource('barangs', BarangController::class)->parameters(['barangs' => 'kodeBarang']);
});

// Invoice routes with nested resource
Route::prefix('divisi/{kodeDivisi}')->group(function () {
    Route::apiResource('invoices', InvoiceController::class)->parameters(['invoices' => 'noInvoice']);
});

// Additional Model Routes

// D Paket routes
Route::prefix('divisi/{kodeDivisi}')->group(function () {
    Route::apiResource('dpakets', DPaketController::class);
});

// D TT routes
Route::prefix('divisi/{kodeDivisi}')->group(function () {
    Route::apiResource('dtts', DTtController::class);
});

// D Voucher routes
Route::prefix('divisi/{kodeDivisi}')->group(function () {
    Route::apiResource('dvouchers', DVoucherController::class);
});

// M Dokumen routes
Route::prefix('divisi/{kodeDivisi}')->group(function () {
    Route::apiResource('mdokumens', MDokumenController::class);
});

// M Resi routes
Route::prefix('divisi/{kodeDivisi}')->group(function () {
    Route::apiResource('mresis', MResiController::class);
});

// M TT routes
Route::prefix('divisi/{kodeDivisi}')->group(function () {
    Route::apiResource('mtts', MTtController::class);
});

// M Voucher routes
Route::prefix('divisi/{kodeDivisi}')->group(function () {
    Route::apiResource('mvouchers', MVoucherController::class);
});

// Penerimaan Finance routes
Route::prefix('divisi/{kodeDivisi}')->group(function () {
    Route::apiResource('penerimaan-finances', PenerimaanFinanceController::class);
});

// Penerimaan Finance Detail routes
Route::prefix('divisi/{kodeDivisi}')->group(function () {
    Route::apiResource('penerimaan-finance-details', PenerimaanFinanceDetailController::class);
});

// Retur Penerimaan routes
Route::prefix('divisi/{kodeDivisi}')->group(function () {
    Route::apiResource('retur-penerimaans', ReturPenerimaanController::class);
});

// Retur Penerimaan Detail routes
Route::prefix('divisi/{kodeDivisi}')->group(function () {
    Route::apiResource('retur-penerimaan-details', ReturPenerimaanDetailController::class);
});

// Return Sales routes
Route::prefix('divisi/{kodeDivisi}')->group(function () {
    Route::apiResource('return-sales', ReturnSalesController::class);
});

// Return Sales Detail routes
Route::prefix('divisi/{kodeDivisi}')->group(function () {
    Route::apiResource('return-sales-details', ReturnSalesDetailController::class);
});

// Saldo Bank routes
Route::prefix('divisi/{kodeDivisi}')->group(function () {
    Route::apiResource('saldo-banks', SaldoBankController::class);
    Route::get('saldo-banks/bank/{kodeBank}', [SaldoBankController::class, 'getByBank']);
    Route::get('saldo-banks/bank/{kodeBank}/latest', [SaldoBankController::class, 'getLatest']);
});

// Stok Minimum routes
Route::prefix('divisi/{kodeDivisi}')->group(function () {
    Route::apiResource('stok-minimums', StokMinimumController::class);
    Route::get('stok-minimums/check/low-stock', [StokMinimumController::class, 'checkLowStock']);
});
