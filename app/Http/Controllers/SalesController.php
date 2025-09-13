<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSalesRequest;
use App\Http\Requests\UpdateSalesRequest;
use App\Http\Resources\SalesResource;
use App\Http\Resources\SalesCollection;
use App\Models\Sales;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SalesController extends Controller
{
    /**
     * Display a listing of sales with pagination and filtering.
     */
    public function index(Request $request, string $kodeDivisi): JsonResponse
    {
        try {
            $request->attributes->set('query_start_time', microtime(true));
            
                $query = Sales::where('kode_divisi', $kodeDivisi)
                                 ->with(['divisi', 'area' => function ($q) use ($kodeDivisi) {
                                     $q->where('kode_divisi', $kodeDivisi);
                                 }]);

            // Apply search filter
            if ($request->filled('search')) {
                $search = $request->get('search');
                $driver = DB::connection()->getDriverName();
                $searchOperator = $driver === 'pgsql' ? 'ILIKE' : 'LIKE';
                $query->where(function ($q) use ($search, $searchOperator) {
                    $q->where('nama_sales', $searchOperator, "%{$search}%")
                      ->orWhere('kode_sales', $searchOperator, "%{$search}%")
                      ->orWhere('alamat', $searchOperator, "%{$search}%")
                      ->orWhere('no_hp', $searchOperator, "%{$search}%");
                });
            }

            // Apply area filter
            if ($request->filled('area')) {
                $query->where('kode_area', $request->get('area'));
            }

            // Apply status filter
            if ($request->filled('status')) {
                $query->where('status', $request->boolean('status'));
            }

            // Apply sorting
            $sortField = $request->get('sort', 'kode_sales');
            $sortDirection = $request->get('direction', 'asc');
            
            $allowedSortFields = ['kode_sales', 'nama_sales', 'kode_area', 'target', 'status'];
            if (in_array($sortField, $allowedSortFields)) {
                $query->orderBy($sortField, $sortDirection);
            }

            // Paginate results
            $perPage = min($request->get('per_page', 15), 100);
            $sales = $query->paginate($perPage);

            return response()->json(new SalesCollection($sales));

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data sales',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created sales.
     */
    public function store(StoreSalesRequest $request, string $kodeDivisi): JsonResponse
    {
        try {
            DB::beginTransaction();

            $salesData = $request->validated();
            $salesData['kode_divisi'] = $kodeDivisi;

            $sales = Sales::create($salesData);
            // Ensure fresh state and relations are loaded with constrained area
            $sales->refresh()->load([
                'divisi',
                'area' => function ($q) use ($kodeDivisi) {
                    $q->where('kode_divisi', $kodeDivisi);
                }
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Sales berhasil dibuat',
                'data' => new SalesResource($sales)
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat sales',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified sales.
     */
    public function show(string $kodeDivisi, string $kodeSales): JsonResponse
    {
        try {
            $sales = Sales::with(['divisi', 'area' => function ($q) use ($kodeDivisi) {
                            $q->where('kode_divisi', $kodeDivisi);
                        }])
                         ->where('kode_divisi', $kodeDivisi)
                         ->where('kode_sales', $kodeSales)
                         ->first();

            if (!$sales) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sales tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => new SalesResource($sales)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data sales',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified sales.
     */
    public function update(UpdateSalesRequest $request, string $kodeDivisi, string $kodeSales): JsonResponse
    {
        try {
            $sales = Sales::where('kode_divisi', $kodeDivisi)
                         ->where('kode_sales', $kodeSales)
                         ->first();

            if (!$sales) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sales tidak ditemukan'
                ], 404);
            }

            DB::beginTransaction();

            // Update manual untuk composite key
            Sales::where('kode_divisi', $kodeDivisi)
                 ->where('kode_sales', $kodeSales)
                 ->update($request->validated());
            
            // Refresh model untuk response
            $sales = Sales::with(['divisi', 'area' => function ($q) use ($kodeDivisi) {
                            $q->where('kode_divisi', $kodeDivisi);
                        }])
                         ->where('kode_divisi', $kodeDivisi)
                         ->where('kode_sales', $kodeSales)
                         ->first();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Sales berhasil diperbarui',
                'data' => new SalesResource($sales)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui sales',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified sales.
     */
    public function destroy(string $kodeDivisi, string $kodeSales): JsonResponse
    {
        try {
            $sales = Sales::where('kode_divisi', $kodeDivisi)
                         ->where('kode_sales', $kodeSales)
                         ->first();

            if (!$sales) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sales tidak ditemukan'
                ], 404);
            }

            DB::beginTransaction();

            // Check if sales has related invoices
            $hasInvoices = $sales->invoices()->exists();
            if ($hasInvoices) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Sales tidak dapat dihapus karena memiliki data invoice terkait'
                ], 422);
            }

            // Delete manual untuk composite key
            Sales::where('kode_divisi', $kodeDivisi)
                 ->where('kode_sales', $kodeSales)
                 ->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Sales berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete sales', [
                'kode_divisi' => $kodeDivisi,
                'kode_sales' => $kodeSales,
                'error' => $e->getMessage(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus sales',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get sales performance statistics.
     */
    public function getSalesStats(string $kodeDivisi, string $kodeSales): JsonResponse
    {
        try {
            $sales = Sales::with(['invoices' => function ($query) {
                $query->selectRaw('kode_sales, kode_divisi, COUNT(*) as total_invoices, SUM(grand_total) as total_sales')
                      ->groupBy('kode_sales', 'kode_divisi');
            }])
            ->where('kode_divisi', $kodeDivisi)
            ->where('kode_sales', $kodeSales)
            ->first();

            if (!$sales) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sales tidak ditemukan'
                ], 404);
            }

            $totalSales = $sales->invoices->sum('grand_total');
            $totalInvoices = $sales->invoices->count();
            $achievement = $sales->target > 0 ? ($totalSales / $sales->target) * 100 : 0;

            return response()->json([
                'success' => true,
                'data' => [
                    'sales_info' => new SalesResource($sales),
                    'performance_stats' => [
                        'total_invoices' => $totalInvoices,
                        'total_sales_value' => $totalSales,
                        'target' => $sales->target,
                        'achievement_percentage' => round($achievement, 2),
                        'status' => $achievement >= 100 ? 'Target Tercapai' : 'Belum Tercapai Target'
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil statistik sales',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
