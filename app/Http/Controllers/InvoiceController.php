<?php
// File: app/Http/Controllers/InvoiceController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\InvoiceRequest;
use App\Models\Invoice;
use App\Models\InvoiceDetail;
use App\Models\MCust;
use App\Models\MSales;
use App\Models\MasterBarang;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class InvoiceController extends Controller
{
    /**
     * Display a listing of invoices with modern filtering and pagination
     */
    public function index(Request $request)
    {
        try {
            $query = Invoice::with(['customer', 'sales', 'divisi'])
                ->orderBy('TGL_INVOICE', 'desc');
            
            // Advanced filtering
            if ($request->has('kode_divisi') && !empty($request->kode_divisi)) {
                $query->where('KODE_DIVISI', $request->kode_divisi);
            }
            
            if ($request->has('kode_cust') && !empty($request->kode_cust)) {
                $query->where('KODE_CUST', $request->kode_cust);
            }
            
            if ($request->has('status') && !empty($request->status)) {
                $query->where('STATUS', $request->status);
            }
            
            if ($request->has('date_from') && !empty($request->date_from)) {
                $query->whereDate('TGL_INVOICE', '>=', $request->date_from);
            }
            
            if ($request->has('date_to') && !empty($request->date_to)) {
                $query->whereDate('TGL_INVOICE', '<=', $request->date_to);
            }
            
            // Search functionality
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('NO_INVOICE', 'like', "%{$search}%")
                      ->orWhereHas('customer', function($customerQuery) use ($search) {
                          $customerQuery->where('NAMA_CUST', 'like', "%{$search}%");
                      });
                });
            }
            
            $perPage = $request->get('per_page', 50);
            $invoices = $query->paginate($perPage);
            
            return response()->json([
                'success' => true,
                'message' => 'Data invoices retrieved successfully',
                'data' => $invoices
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve invoices data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created invoice using stored procedure
     */
    public function store(InvoiceRequest $request)
    {
        DB::beginTransaction();
        
        try {
            $validatedData = $request->validated();
            
            // Generate nomor invoice jika belum ada
            if (empty($validatedData['no_invoice'])) {
                $nomorBaru = DB::select('CALL sp_set_nomor(?, ?, ?)', [
                    $validatedData['kode_divisi'],
                    'INVOICE',
                    null
                ]);
                $validatedData['no_invoice'] = $nomorBaru[0]->nomor ?? '';
            }
            
            // Memanggil Stored Procedure untuk menangani semua logika invoice, stok, dan jurnal
            foreach ($validatedData['details'] as $detail) {
                DB::select('CALL sp_invoice(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [
                    $validatedData['kode_divisi'],
                    $validatedData['no_invoice'],
                    $validatedData['kode_cust'],
                    $validatedData['kode_sales'],
                    $validatedData['tipe'],
                    $validatedData['total'],
                    $validatedData['disc'] ?? 0,
                    $validatedData['pajak'] ?? 0,
                    $validatedData['grand_total'],
                    $validatedData['ket'] ?? '',
                    $detail['kode_barang'],
                    $detail['qty_supply'],
                    $detail['harga_jual'],
                    $detail['diskon1'] ?? 0,
                    $detail['diskon2'] ?? 0,
                    $detail['harga_nett'],
                    auth()->user()->name ?? 'system'
                ]);
            }
            
            // Buat jurnal otomatis
            DB::select('CALL sp_journal_invoice(?)', [$validatedData['no_invoice']]);
            
            // Ambil invoice yang baru dibuat
            $invoice = Invoice::where('NO_INVOICE', $validatedData['no_invoice'])
                ->where('KODE_DIVISI', $validatedData['kode_divisi'])
                ->with(['details', 'customer', 'sales'])
                ->first();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Invoice created successfully',
                'data' => $invoice
            ], 201);
            
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'error' => 'Failed to create invoice: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified invoice
     */
    public function show(string $kodeDivisi, string $noInvoice)
    {
        try {
            $invoice = Invoice::where('KODE_DIVISI', $kodeDivisi)
                ->where('NO_INVOICE', $noInvoice)
                ->with(['details.masterBarang', 'customer', 'sales', 'divisi'])
                ->first();
            
            if (!$invoice) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invoice not found'
                ], 404);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Invoice retrieved successfully',
                'data' => $invoice
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve invoice',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancel invoice using stored procedure
     */
    public function cancel(string $kodeDivisi, string $noInvoice)
    {
        DB::beginTransaction();
        
        try {
            // Memanggil Stored Procedure untuk pembatalan invoice
            // SP akan handle: kembalikan stok, hapus jurnal, update status
            DB::select('CALL sp_pembatalan_invoice(?, ?)', [
                $kodeDivisi,
                $noInvoice
            ]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Invoice cancelled successfully'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'error' => 'Failed to cancel invoice: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get invoice view for reports
     */
    public function getInvoiceView(Request $request)
    {
        try {
            // Menggunakan view yang sudah ada di database untuk efisiensi
            $query = DB::table('v_invoice');
            
            // Apply filters if provided
            if ($request->has('kode_divisi')) {
                $query->where('KODE_DIVISI', $request->kode_divisi);
            }
            
            if ($request->has('date_from')) {
                $query->whereDate('TGL_INVOICE', '>=', $request->date_from);
            }
            
            if ($request->has('date_to')) {
                $query->whereDate('TGL_INVOICE', '<=', $request->date_to);
            }
            
            $data = $query->get();
            
            return response()->json([
                'success' => true,
                'message' => 'Invoice view data retrieved successfully',
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve invoice view data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get invoice summary for dashboard
     */
    public function getSummary(Request $request)
    {
        try {
            $kodeDivisi = $request->get('kode_divisi');
            $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth());
            $dateTo = $request->get('date_to', Carbon::now()->endOfMonth());
            
            $summary = DB::select("
                SELECT 
                    COUNT(*) as total_invoice,
                    SUM(CASE WHEN STATUS != 'Cancel' THEN GRAND_TOTAL ELSE 0 END) as total_penjualan,
                    SUM(CASE WHEN STATUS = 'Cancel' THEN 1 ELSE 0 END) as total_cancel,
                    AVG(CASE WHEN STATUS != 'Cancel' THEN GRAND_TOTAL ELSE NULL END) as avg_penjualan
                FROM INVOICE 
                WHERE TGL_INVOICE BETWEEN ? AND ?
                " . ($kodeDivisi ? "AND KODE_DIVISI = ?" : ""),
                array_filter([$dateFrom, $dateTo, $kodeDivisi])
            );
            
            return response()->json([
                'success' => true,
                'message' => 'Invoice summary retrieved successfully',
                'data' => $summary[0] ?? []
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve invoice summary',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}


