<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInvoiceRequest;
use App\Http\Requests\UpdateInvoiceRequest;
use App\Http\Resources\InvoiceResource;
use App\Http\Resources\InvoiceCollection;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    /**
     * Display a listing of invoices with pagination and filtering.
     */
    public function index(Request $request, string $kodeDivisi): JsonResponse
    {
        try {
            $request->attributes->set('query_start_time', microtime(true));
            
            $query = Invoice::where('kode_divisi', $kodeDivisi)
                           ->with(['divisi', 'customer', 'sales']);

            // Apply search filter
            if ($request->filled('search')) {
                $search = $request->get('search');
                $driver = DB::connection()->getDriverName();
                $searchOperator = $driver === 'pgsql' ? 'ILIKE' : 'LIKE';
                $query->where(function ($q) use ($search, $searchOperator) {
                    $q->where('no_invoice', $searchOperator, "%{$search}%")
                      ->orWhere('ket', $searchOperator, "%{$search}%")
                      ->orWhereHas('customer', function ($customerQuery) use ($search, $searchOperator) {
                          $customerQuery->where('nama_cust', $searchOperator, "%{$search}%");
                      });
                });
            }

            // Apply customer filter
            if ($request->filled('customer')) {
                $query->where('kode_cust', $request->get('customer'));
            }

            // Apply sales filter
            if ($request->filled('sales')) {
                $query->where('kode_sales', $request->get('sales'));
            }

            // Apply status filter
            if ($request->filled('status')) {
                $query->where('status', $request->get('status'));
            }

            // Apply date range filter
            if ($request->filled('date_from')) {
                $query->whereDate('tgl_faktur', '>=', $request->get('date_from'));
            }
            if ($request->filled('date_to')) {
                $query->whereDate('tgl_faktur', '<=', $request->get('date_to'));
            }

            // Apply overdue filter
            if ($request->boolean('overdue_only')) {
                $query->where('jatuh_tempo', '<', now())
                      ->where('sisa_invoice', '>', 0)
                      ->whereNotIn('status', ['Lunas', 'Cancel']);
            }

            // Apply sorting
            $sortField = $request->get('sort', 'tgl_faktur');
            $sortDirection = $request->get('direction', 'desc');
            
            $allowedSortFields = ['no_invoice', 'tgl_faktur', 'jatuh_tempo', 'grand_total', 'sisa_invoice', 'status'];
            if (in_array($sortField, $allowedSortFields)) {
                $query->orderBy($sortField, $sortDirection);
            }

            // Paginate results
            $perPage = min($request->get('per_page', 15), 100);
            $invoices = $query->paginate($perPage);

            return (new InvoiceCollection($invoices))
                ->additional(['success' => true])
                ->response()
                ->setStatusCode(200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data invoice',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created invoice.
     */
    public function store(StoreInvoiceRequest $request, string $kodeDivisi): JsonResponse
    {
        try {
            DB::beginTransaction();

            $invoiceData = $request->validated();
            $invoiceData['kode_divisi'] = $kodeDivisi;

            $invoice = Invoice::create($invoiceData);
            $invoice->load(['divisi', 'customer', 'sales']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Invoice berhasil dibuat',
                'data' => new InvoiceResource($invoice)
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat invoice',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified invoice.
     */
    public function show(string $kodeDivisi, string $noInvoice): JsonResponse
    {
        try {
            $kodeDivisi = strtoupper($kodeDivisi);
            $noInvoice = strtoupper($noInvoice);
            $invoice = Invoice::with(['divisi', 'customer', 'sales', 'invoiceDetails'])
                             ->where('kode_divisi', $kodeDivisi)
                             ->where('no_invoice', $noInvoice)
                             ->first();

            if (!$invoice) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invoice tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => new InvoiceResource($invoice)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data invoice',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified invoice.
     */
    public function update(UpdateInvoiceRequest $request, string $kodeDivisi, string $noInvoice): JsonResponse
    {
        try {
            $kodeDivisi = strtoupper($kodeDivisi);
            $noInvoice = strtoupper($noInvoice);
            $invoice = Invoice::where('kode_divisi', $kodeDivisi)
                             ->where('no_invoice', $noInvoice)
                             ->first();

            if (!$invoice) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invoice tidak ditemukan'
                ], 404);
            }

            // Check if invoice can be edited
            if (!in_array($invoice->status, ['Open', 'Belum Lunas'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invoice dengan status ' . $invoice->status . ' tidak dapat diubah'
                ], 422);
            }

            DB::beginTransaction();

            // Update manual untuk composite key
            Invoice::where('kode_divisi', $kodeDivisi)
                   ->where('no_invoice', $noInvoice)
                   ->update($request->validated());
            
            // Refresh model untuk response
            $invoice = Invoice::with(['divisi', 'customer', 'sales'])
                             ->where('kode_divisi', $kodeDivisi)
                             ->where('no_invoice', $noInvoice)
                             ->first();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Invoice berhasil diperbarui',
                'data' => new InvoiceResource($invoice)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui invoice',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified invoice.
     */
    public function destroy(string $kodeDivisi, string $noInvoice): JsonResponse
    {
        try {
            $kodeDivisi = strtoupper($kodeDivisi);
            $noInvoice = strtoupper($noInvoice);
            $invoice = Invoice::where('kode_divisi', $kodeDivisi)
                             ->where('no_invoice', $noInvoice)
                             ->first();

            if (!$invoice) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invoice tidak ditemukan'
                ], 404);
            }

            // Check if invoice can be deleted
            if (!in_array($invoice->status, ['Open']) || $invoice->sisa_invoice != $invoice->grand_total) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invoice tidak dapat dihapus. Hanya invoice dengan status Open dan belum ada pembayaran yang dapat dihapus'
                ], 422);
            }

            DB::beginTransaction();

            // Check if invoice has details
            $hasDetails = $invoice->invoiceDetails()->exists();
            if ($hasDetails) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invoice tidak dapat dihapus karena memiliki detail item. Hapus detail terlebih dahulu atau ubah status menjadi Cancel'
                ], 422);
            }

            // Hard delete for invoice using composite key constraints
            Invoice::where('kode_divisi', $kodeDivisi)
                ->where('no_invoice', $noInvoice)
                ->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Invoice berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus invoice',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancel the specified invoice.
     */
    public function cancel(string $kodeDivisi, string $noInvoice): JsonResponse
    {
        try {
            $kodeDivisi = strtoupper($kodeDivisi);
            $noInvoice = strtoupper($noInvoice);
            $invoice = Invoice::where('kode_divisi', $kodeDivisi)
                             ->where('no_invoice', $noInvoice)
                             ->first();

            if (!$invoice) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invoice tidak ditemukan'
                ], 404);
            }

            // Check if invoice can be cancelled
            if (!in_array($invoice->status, ['Open', 'Belum Lunas']) || $invoice->sisa_invoice != $invoice->grand_total) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invoice tidak dapat dibatalkan. Hanya invoice yang belum ada pembayaran yang dapat dibatalkan'
                ], 422);
            }

            DB::beginTransaction();

            $invoice->update([
                'status' => 'Cancel'
            ]);

            // Reload without using fresh() to avoid composite key issues
            $reloaded = Invoice::with(['divisi', 'customer', 'sales'])
                ->where('kode_divisi', $kodeDivisi)
                ->where('no_invoice', $noInvoice)
                ->first();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Invoice berhasil dibatalkan',
                'data' => new InvoiceResource($reloaded)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal membatalkan invoice',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get invoice summary statistics.
     */
    public function getSummary(Request $request, string $kodeDivisi): JsonResponse
    {
        try {
            $query = Invoice::where('kode_divisi', $kodeDivisi);

            // Apply date filter if provided
            if ($request->filled('month')) {
                $month = $request->get('month');
                $year = $request->get('year', date('Y'));
                $query->whereYear('tgl_faktur', $year)
                      ->whereMonth('tgl_faktur', $month);
            }

            $invoices = $query->get();

            $summary = [
                'total_invoices' => $invoices->count(),
                'total_value' => $invoices->sum('grand_total'),
                'total_outstanding' => $invoices->sum('sisa_invoice'),
                'total_paid' => $invoices->sum('grand_total') - $invoices->sum('sisa_invoice'),
                'status_breakdown' => [
                    'open' => $invoices->where('status', 'Open')->count(),
                    'lunas' => $invoices->where('status', 'Lunas')->count(),
                    'belum_lunas' => $invoices->where('status', 'Belum Lunas')->count(),
                    'partial' => $invoices->where('status', 'Partial')->count(),
                    'cancel' => $invoices->where('status', 'Cancel')->count(),
                ],
                'overdue_count' => $invoices->filter(function ($invoice) {
                    return $invoice->jatuh_tempo && 
                           now()->isAfter($invoice->jatuh_tempo) && 
                           $invoice->sisa_invoice > 0 &&
                           $invoice->status !== 'Lunas';
                })->count(),
                'due_today' => $invoices->filter(function ($invoice) {
                    return $invoice->jatuh_tempo && 
                           $invoice->jatuh_tempo->isToday() && 
                           $invoice->sisa_invoice > 0 &&
                           $invoice->status !== 'Lunas';
                })->count()
            ];

            return response()->json([
                'success' => true,
                'data' => $summary
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil ringkasan invoice',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
