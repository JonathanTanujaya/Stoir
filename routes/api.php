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
use App\Http\Controllers\KategoriController;
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
use App\Http\Controllers\UserController;
use App\Http\Controllers\InvoiceDetailController;
use App\Http\Controllers\PartPenerimaanDetailController;
use App\Http\Controllers\DBankController;
use App\Http\Controllers\DBarangController;
use App\Http\Controllers\DatabaseTestController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// PUBLIC: Reports (read-only)
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

// PUBLIC: Procedure endpoints (mutating business logic)
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

// PUBLIC: Core routes (full CRUD where applicable)
// Divisi
Route::get('divisi/stats', [DivisiController::class, 'stats']);
Route::apiResource('divisi', DivisiController::class)
    ->parameters(['divisi' => 'kodeDivisi']);

// Nested under divisi (read-only)
Route::prefix('divisi/{kodeDivisi}')->group(function () {
    // Banks
    Route::apiResource('banks', BankController::class)
        ->parameters(['banks' => 'kodeBank']);

    // Areas
    Route::get('areas/stats', [AreaController::class, 'stats']);
    Route::apiResource('areas', AreaController::class)
        ->parameters(['areas' => 'kodeArea']);

    // Kategoris
    Route::get('kategoris/stats', [KategoriController::class, 'stats']);
    Route::apiResource('kategoris', KategoriController::class)
        ->parameters(['kategoris' => 'kodeKategori']);

    // Sales
    Route::apiResource('sales', SalesController::class)
        ->parameters(['sales' => 'kodeSales']);
    Route::get('sales/{kodeSales}/stats', [SalesController::class, 'getSalesStats']);

    // Customers
    Route::apiResource('customers', CustomerController::class)
        ->parameters(['customers' => 'kodeCust']);
    Route::get('customers/{kodeCust}/credit-info', [CustomerController::class, 'getCreditInfo']);
    Route::get('areas/{kodeArea}/customers', [CustomerController::class, 'getByArea']);
    Route::get('sales/{kodeSales}/customers', [CustomerController::class, 'getBySales']);

    // Suppliers
    Route::apiResource('suppliers', SupplierController::class)
        ->parameters(['suppliers' => 'kodeSupplier']);
    Route::get('suppliers/{kodeSupplier}/stats', [SupplierController::class, 'getSupplierStats']);

    // Barangs
    Route::apiResource('barangs', BarangController::class)
        ->parameters(['barangs' => 'kodeBarang']);
    Route::get('barangs/{kodeBarang}/stock-info', [BarangController::class, 'getStockInfo']);
    Route::get('categories', [BarangController::class, 'getCategories']);

    // Invoices
    Route::apiResource('invoices', InvoiceController::class)
        ->parameters(['invoices' => 'noInvoice']);
    Route::get('invoices-summary', [InvoiceController::class, 'getSummary']);
    Route::patch('invoices/{noInvoice}/cancel', [InvoiceController::class, 'cancel']);

    // Invoice details (nested)
    Route::prefix('invoices/{noInvoice}')->group(function () {
        Route::apiResource('details', InvoiceDetailController::class);
        Route::get('details-stats', [InvoiceDetailController::class, 'stats']);
    });

    // Saldo Banks
    Route::apiResource('saldo-banks', SaldoBankController::class);
    Route::get('saldo-banks/bank/{kodeBank}', [SaldoBankController::class, 'getByBank']);
    Route::get('saldo-banks/bank/{kodeBank}/latest', [SaldoBankController::class, 'getLatest']);
});

// AUTHENTICATED: Database Testing only
Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('test')->group(function () {
        Route::get('/connection', [DatabaseTestController::class, 'testConnection']);
        Route::get('/views', [DatabaseTestController::class, 'testViews']);
        Route::get('/procedures', [DatabaseTestController::class, 'testProcedures']);
        Route::get('/foreign-keys', [DatabaseTestController::class, 'testForeignKeys']);
        Route::get('/models', [DatabaseTestController::class, 'testModelRelationships']);
        Route::get('/full', [DatabaseTestController::class, 'fullTest']);
    });
});

// PUBLIC: Additional modules (full CRUD)
Route::prefix('divisi/{kodeDivisi}')->group(function () {
    Route::apiResource('dpakets', DPaketController::class);
    Route::apiResource('dtts', DTtController::class);
    Route::apiResource('dvouchers', DVoucherController::class);
    Route::apiResource('mdokumens', MDokumenController::class);
    Route::apiResource('mresis', MResiController::class);
    Route::apiResource('mtts', MTtController::class);
    Route::apiResource('mvouchers', MVoucherController::class);
    Route::apiResource('penerimaan-finances', PenerimaanFinanceController::class);
    Route::apiResource('penerimaan-finance-details', PenerimaanFinanceDetailController::class);
    Route::apiResource('retur-penerimaans', ReturPenerimaanController::class);
    Route::prefix('retur-penerimaan/{noRetur}')->group(function () {
        Route::apiResource('details', ReturPenerimaanDetailController::class);
        Route::get('details-stats', [ReturPenerimaanDetailController::class, 'stats']);
    });
    Route::apiResource('return-sales', ReturnSalesController::class);
    Route::prefix('return-sales/{noRetur}')->group(function () {
        Route::apiResource('details', ReturnSalesDetailController::class);
        Route::get('details-stats', [ReturnSalesDetailController::class, 'stats']);
    });
    Route::apiResource('stok-minimums', StokMinimumController::class);
    Route::apiResource('users', UserController::class)->parameters(['users' => 'username']);
    Route::get('users-stats', [UserController::class, 'stats']);

    // Part Penerimaan Detail routes (limited in controller)
    Route::prefix('part-penerimaan/{noPenerimaan}')->group(function () {
        Route::get('details', [PartPenerimaanDetailController::class, 'index']);
        Route::post('details', [PartPenerimaanDetailController::class, 'store']);
        Route::get('details-stats', [PartPenerimaanDetailController::class, 'stats']);
        Route::delete('details-bulk', [PartPenerimaanDetailController::class, 'bulkDelete']);
    });

    // Bank Account Detail routes
    Route::apiResource('bank-accounts', DBankController::class)->parameters(['bank-accounts' => 'noRekening']);
    Route::get('bank-accounts-statistics', [DBankController::class, 'statistics']);

    // Barang Detail routes
    Route::prefix('barangs/{kodeBarang}')->group(function () {
        Route::apiResource('details', DBarangController::class);
        Route::get('details-statistics', [DBarangController::class, 'statistics']);
    });
});

