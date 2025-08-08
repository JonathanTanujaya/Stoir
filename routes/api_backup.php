<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Test endpoint for debugging
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

// Seed sample data endpoint
Route::post('/seed-sample-data', function () {
    try {
        // Get table structure first
        $columns = \DB::select("SELECT column_name FROM information_schema.columns WHERE table_name = 'm_cust' ORDER BY ordinal_position");
        
        // Prepare minimal data based on available columns
        $sampleData = [
            'kodedivisi' => '01',
            'namacust' => 'Test Customer 1'
        ];
        
        // Check which columns exist and add appropriate data
        $availableColumns = array_column($columns, 'column_name');
        
        if (in_array('kodecust', $availableColumns)) $sampleData['kodecust'] = 'CUST001';
        if (in_array('alamat', $availableColumns)) $sampleData['alamat'] = 'Jl. Test No. 123';
        if (in_array('telp', $availableColumns)) $sampleData['telp'] = '081234567890';
        
        // Insert data
        \DB::table('m_cust')->insert($sampleData);
        
        return response()->json([
            'success' => true,
            'message' => 'Sample data inserted',
            'available_columns' => $availableColumns,
            'inserted_data' => $sampleData
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to insert sample data',
            'error' => $e->getMessage()
        ], 500);
    }
});

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
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
Route::post('auth/login', [AuthController::class, 'login']);
Route::post('auth/register', [AuthController::class, 'register'])->middleware(['auth:sanctum', 'admin']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('auth/logout', [AuthController::class, 'logout']);
    Route::get('auth/me', [AuthController::class, 'me']);
    Route::post('auth/change-password', [AuthController::class, 'changePassword']);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/*
|--------------------------------------------------------------------------
| Master Data Routes
|--------------------------------------------------------------------------
*/
// Areas routes with composite keys
Route::get('areas', [AreaController::class, 'index']);
Route::post('areas', [AreaController::class, 'store']);
Route::get('areas/{kodeDivisi}', [AreaController::class, 'showByDivisi']); // NEW: Show by division only
Route::get('areas/{kodeDivisi}/{kodeArea}', [AreaController::class, 'show']);
Route::put('areas/{kodeDivisi}/{kodeArea}', [AreaController::class, 'update']);
Route::delete('areas/{kodeDivisi}/{kodeArea}', [AreaController::class, 'destroy']);

// Barang routes with composite keys
Route::get('barang', [BarangController::class, 'index']);
Route::post('barang', [BarangController::class, 'store']);
Route::get('barang/{kodeDivisi}', [BarangController::class, 'showByDivisi']); // NEW: Show by division only
Route::get('barang/{kodeDivisi}/{kodeBarang}', [BarangController::class, 'show']);
Route::put('barang/{kodeDivisi}/{kodeBarang}', [BarangController::class, 'update']);
Route::delete('barang/{kodeDivisi}/{kodeBarang}', [BarangController::class, 'destroy']);
Route::get('vbarang', [BarangController::class, 'getVBarang']);

// Kategori routes with composite keys
Route::get('kategori', [KategoriController::class, 'index']);
Route::post('kategori', [KategoriController::class, 'store']);
Route::get('kategori/{kodeDivisi}', [KategoriController::class, 'showByDivisi']); // NEW: Show by division only
Route::get('kategori/{kodeDivisi}/{kodeKategori}', [KategoriController::class, 'show']);
Route::put('kategori/{kodeDivisi}/{kodeKategori}', [KategoriController::class, 'update']);
Route::delete('kategori/{kodeDivisi}/{kodeKategori}', [KategoriController::class, 'destroy']);

// Route::apiResource('banks', MBankController::class);

// Customer routes with composite keys
Route::get('customers', [CustomerController::class, 'index']);
Route::post('customers', [CustomerController::class, 'store']);
Route::get('customers/{kodeDivisi}', [CustomerController::class, 'showByDivisi']); // NEW: Show by division only
Route::get('customers/{kodeDivisi}/{kodeCust}', [CustomerController::class, 'show']);
Route::put('customers/{kodeDivisi}/{kodeCust}', [CustomerController::class, 'update']);
Route::delete('customers/{kodeDivisi}/{kodeCust}', [CustomerController::class, 'destroy']);

// Route::apiResource('kategoris', KategoriController::class);
// Route::apiResource('banks', MBankController::class);
// Route::apiResource('resis', MResiController::class);
// Route::apiResource('tts', MTTController::class);
// Route::apiResource('trans', MTransController::class);
// Route::apiResource('vouchers', MVoucherController::class);

// Sales routes with composite keys
Route::get('sales', [SalesController::class, 'index']);
Route::post('sales', [SalesController::class, 'store']);
Route::get('sales/{kodeDivisi}', [SalesController::class, 'showByDivisi']); // NEW: Show by division only
Route::get('sales/{kodeDivisi}/{kodeSales}', [SalesController::class, 'show']);
Route::put('sales/{kodeDivisi}/{kodeSales}', [SalesController::class, 'update']);
Route::delete('sales/{kodeDivisi}/{kodeSales}', [SalesController::class, 'destroy']);

// Master Data Routes
Route::apiResource('suppliers', SupplierController::class);
Route::apiResource('mcoa', MCOAController::class);
Route::apiResource('mdivisi', MDivisiController::class);
Route::apiResource('mdokumen', MDokumenController::class);
Route::apiResource('master-user', MasterUserController::class);

/*
|--------------------------------------------------------------------------
| Existing Transaction Routes
|--------------------------------------------------------------------------
*/
// Route::apiResource('claims', ClaimController::class);
// Route::apiResource('invoices', InvoiceController::class);
// Route::apiResource('journals', JournalController::class);
// Route::apiResource('part-penerimaan', PartPenerimaanController::class);
// Route::apiResource('penerimaan-finance', PenerimaanFinanceController::class);
// Route::apiResource('return-sales', ReturnSalesController::class);

/*
|--------------------------------------------------------------------------
| New Transaction & Feature Routes
|--------------------------------------------------------------------------
*/
// Route::apiResource('part-penerimaan-bonus', PartPenerimaanBonusController::class);
// Route::apiResource('retur-penerimaan', ReturPenerimaanController::class);
// Route::apiResource('saldo-bank', SaldoBankController::class);
// Route::apiResource('spv', SPVController::class);
// Route::apiResource('stok-claim', StokClaimController::class);
// Route::apiResource('stok-minimum', StokMinimumController::class);
// Route::apiResource('tmp-print-tt', TmpPrintTTController::class);
// Route::apiResource('user-module', UserModuleController::class);
// Route::apiResource('d-paket', DPaketController::class);
// Route::apiResource('merge-barang', MergeBarangController::class);
// Route::apiResource('opname', OpnameController::class);

/*
|--------------------------------------------------------------------------
| Custom View & Action Routes
|--------------------------------------------------------------------------
*/
/*
// Existing custom routes
Route::get('customer-name', [CustomerController::class, 'getNamaCust']);
Route::get('v-barang', [BarangController::class, 'getVBarang']);
Route::get('supplier-name', [SupplierController::class, 'getNamaSupplier']);
Route::get('v-invoice', [InvoiceController::class, 'getVInvoice']);
Route::get('v-invoice-header', [InvoiceController::class, 'getVInvoiceHeader']);
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
Route::get('v-cust-retur', [ReturnSalesController::class, 'getVCustRetur']);
Route::get('v-return-sales-detail', [ReturnSalesController::class, 'getVReturnSalesDetail']);
Route::get('v-customer-resi', [MResiController::class, 'getVCustomerResi']);
Route::get('v-bank', [MBankController::class, 'getVBank']);
Route::get('v-tt', [MTTController::class, 'getVTT']);
Route::get('v-tt-invoice', [MTTController::class, 'getVTTInvoice']);
Route::get('v-tt-retur', [MTTController::class, 'getVTTRetur']);
Route::get('v-part-penerimaan-bonus-header', [PartPenerimaanBonusController::class, 'getVPartPenerimaanBonusHeader']);
*/
