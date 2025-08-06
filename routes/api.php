<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Master Data Controllers
use App\Http\Controllers\AreaController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\MBankController;
use App\Http\Controllers\MResiController;
use App\Http\Controllers\MTTController;
use App\Http\Controllers\MTransController;
use App\Http\Controllers\MVoucherController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\SupplierController;

// Transaction Controllers
use App\Http\Controllers\ClaimController;
use App\Http\Controllers\InvoiceBonusController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\JournalController;
use App\Http\Controllers\KartuStokController;
use App\Http\Controllers\PartPenerimaanController;
use App\Http\Controllers\PenerimaanFinanceController;
use App\Http\Controllers\ReturnSalesController;

// New Transaction Controllers
use App\Http\Controllers\PartPenerimaanBonusController;
use App\Http\Controllers\ReturPenerimaanController;
use App\Http\Controllers\SaldoBankController;
use App\Http\Controllers\SPVController;
use App\Http\Controllers\StokClaimController;
use App\Http\Controllers\StokMinimumController;
use App\Http\Controllers\TmpPrintTTController;
use App\Http\Controllers\UserModuleController;
use App\Http\Controllers\DPaketController;
use App\Http\Controllers\MergeBarangController;
use App\Http\Controllers\OpnameController;


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/*
|--------------------------------------------------------------------------
| Master Data Routes
|--------------------------------------------------------------------------
*/
Route::apiResource('areas', AreaController::class);
Route::apiResource('barang', BarangController::class);
Route::apiResource('customers', CustomerController::class);
Route::apiResource('kategoris', KategoriController::class);
Route::apiResource('banks', MBankController::class);
Route::apiResource('resis', MResiController::class);
Route::apiResource('tts', MTTController::class);
Route::apiResource('trans', MTransController::class);
Route::apiResource('vouchers', MVoucherController::class);
Route::apiResource('sales', SalesController::class);
Route::apiResource('suppliers', SupplierController::class);
Route::apiResource('mcoas', MCOAController::class);
Route::apiResource('mdivisis', MDivisiController::class);
Route::apiResource('mdokumens', MDokumenController::class);
Route::apiResource('master-users', MasterUserController::class);

/*
|--------------------------------------------------------------------------
| Existing Transaction Routes
|--------------------------------------------------------------------------
*/
Route::apiResource('claims', ClaimController::class);
Route::apiResource('invoice-bonus', InvoiceBonusController::class);
Route::apiResource('invoices', InvoiceController::class);
Route::apiResource('journals', JournalController::class);
Route::apiResource('kartu-stok', KartuStokController::class);
Route::apiResource('part-penerimaan', PartPenerimaanController::class);
Route::apiResource('penerimaan-finance', PenerimaanFinanceController::class);
Route::apiResource('return-sales', ReturnSalesController::class);

/*
|--------------------------------------------------------------------------
| New Transaction & Feature Routes
|--------------------------------------------------------------------------
*/
Route::apiResource('part-penerimaan-bonus', PartPenerimaanBonusController::class);
Route::apiResource('retur-penerimaan', ReturPenerimaanController::class);
Route::apiResource('saldo-bank', SaldoBankController::class);
Route::apiResource('spv', SPVController::class);
Route::apiResource('stok-claim', StokClaimController::class);
Route::apiResource('stok-minimum', StokMinimumController::class);
Route::apiResource('tmp-print-tt', TmpPrintTTController::class);
Route::apiResource('user-module', UserModuleController::class);
Route::apiResource('d-paket', DPaketController::class);
Route::apiResource('merge-barang', MergeBarangController::class);
Route::apiResource('opname', OpnameController::class);

/*
|--------------------------------------------------------------------------
| Custom View & Action Routes
|--------------------------------------------------------------------------
*/
// Existing custom routes
Route::get('customer-name', [CustomerController::class, 'getNamaCust']);
Route::get('v-barang', [BarangController::class, 'getVBarang']);
Route::get('supplier-name', [SupplierController::class, 'getNamaSupplier']);
Route::get('v-invoice', [InvoiceController::class, 'getVInvoice']);
Route::get('v-invoice-header', [InvoiceController::class, 'getVInvoiceHeader']);
Route::get('v-invoice-bonus-header', [InvoiceBonusController::class, 'getVInvoiceBonusHeader']);
Route::get('v-invoice-bonus', [InvoiceBonusController::class, 'getVInvoiceBonus']);
Route::post('journal-invoice', [JournalController::class, 'store']);
Route::post('journal-retur-sales', [JournalController::class, 'storeReturSales']);
Route::get('v-trans', [MTransController::class, 'getVTrans']);
Route::get('v-journal', [JournalController::class, 'getVJournal']);
Route::get('v-claim-detail', [ClaimController::class, 'getVClaimDetail']);
Route::get('v-cust-claim', [ClaimController::class, 'getVCustClaim']);
Route::get('v-voucher', [MVoucherController::class, 'getVVoucher']);
Route::get('v-penerimaan-finance', [PenerimaanFinanceController::class, 'getVPenerimaanFinance']);
Route::get('v-penerimaan-finance-detail', [PenerimaanFinanceController::class, 'getVPenerimaanFinanceDetail']);
Route::get('v-part-penerimaan', [PartPenerimaanController::class, 'getVPartPenerimaan']);
Route::get('v-part-penerimaan-header', [PartPenerimaanController::class, 'getVPartPenerimaanHeader']);
Route::get('v-kartu-stok', [KartuStokController::class, 'getVKartuStok']);
Route::get('v-cust-retur', [ReturnSalesController::class, 'getVCustRetur']);
Route::get('v-return-sales-detail', [ReturnSalesController::class, 'getVReturnSalesDetail']);
Route::get('v-customer-resi', [MResiController::class, 'getVCustomerResi']);
Route::get('v-bank', [MBankController::class, 'getVBank']);
Route::get('v-tt', [MTTController::class, 'getVTT']);
Route::get('v-tt-invoice', [MTTController::class, 'getVTTInvoice']);
Route::get('v-tt-retur', [MTTController::class, 'getVTTRetur']);
Route::get('v-part-penerimaan-bonus-header', [PartPenerimaanBonusController::class, 'getVPartPenerimaanBonusHeader']);