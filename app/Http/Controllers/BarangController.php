<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Divisi;
use App\Models\Kategori;
use App\Http\Requests\StoreBarangRequest;
use App\Http\Requests\UpdateBarangRequest;
use App\Http\Resources\BarangResource;
use App\Http\Resources\BarangCollection;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class BarangController extends Controller
{
    /**
     * Display a paginated listing of barang for a specific divisi
     */
    public function index(Request $request, string $kodeDivisi): JsonResponse
    {
        try {
            // Start timing for collection meta
            $request->attributes->set('query_start_time', microtime(true));
            // Validate divisi exists
            $divisi = Divisi::where('kode_divisi', $kodeDivisi)->firstOrFail();
            
            $query = Barang::with([
                    'divisi',
                    'kategori' => function ($q) use ($kodeDivisi) {
                        $q->where('kode_divisi', $kodeDivisi);
                    }
                ])
                ->withCount(['kartuStoks', 'invoiceDetails'])
                ->where('kode_divisi', $kodeDivisi);

            // Add search functionality
            if ($request->filled('search')) {
                $search = $request->get('search');
                $driver = DB::connection()->getDriverName();
                $searchOperator = $driver === 'pgsql' ? 'ILIKE' : 'LIKE';
                $query->where(function($q) use ($search, $searchOperator) {
                    $q->where('nama_barang', $searchOperator, "%{$search}%")
                      ->orWhere('kode_barang', $searchOperator, "%{$search}%")
                      ->orWhere('merk', $searchOperator, "%{$search}%")
                      ->orWhere('barcode', $searchOperator, "%{$search}%");
                });
            }

            // Filter by category
            if ($request->filled('kategori')) {
                $query->where('kode_kategori', $request->get('kategori'));
            }

            // Filter by status
            if ($request->filled('status')) {
                $query->where('status', $request->boolean('status'));
            }

            // Filter by location
            if ($request->filled('lokasi')) {
                $driver = DB::connection()->getDriverName();
                $searchOperator = $driver === 'pgsql' ? 'ILIKE' : 'LIKE';
                $query->where('lokasi', $searchOperator, "%{$request->get('lokasi')}%");
            }

            // Sort options
            $sortField = $request->get('sort', 'nama_barang');
            $sortDirection = $request->get('direction', 'asc');
            
            $allowedSorts = ['kode_barang', 'nama_barang', 'harga_jual', 'harga_list', 'merk', 'lokasi', 'status', 'created_at', 'updated_at'];
            if (in_array($sortField, $allowedSorts)) {
                $query->orderBy($sortField, $sortDirection);
            }

            $perPage = min($request->get('per_page', 15), 100); // Max 100 items per page
            $barangs = $query->paginate($perPage);

            return (new BarangCollection($barangs))
                ->additional([
                    'success' => true,
                    'divisi' => [
                        'kode_divisi' => $divisi->kode_divisi,
                        'nama_divisi' => $divisi->nama_divisi
                    ]
                ])
                ->response()
                ->setStatusCode(200);

        } catch (\Exception $e) {
            Log::error('Error fetching barangs', [
                'kode_divisi' => $kodeDivisi,
                'error' => $e->getMessage(),
                'request_params' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch barangs',
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Store a newly created barang
     */
    public function store(StoreBarangRequest $request, string $kodeDivisi): JsonResponse
    {
        try {
            // Validate divisi exists
            $divisi = Divisi::where('kode_divisi', $kodeDivisi)->firstOrFail();

            $validated = $request->validated();
            $validated['kode_divisi'] = $kodeDivisi;

            DB::beginTransaction();
            
            $barang = Barang::create($validated);
            
            // Load relationships for response
            $barang->load([
                'divisi',
                'kategori' => function ($q) use ($kodeDivisi) {
                    $q->where('kode_divisi', $kodeDivisi);
                }
            ]);
            
            DB::commit();
            
            Log::info('Barang created successfully', [
                'kode_divisi' => $kodeDivisi,
                'kode_barang' => $barang->kode_barang,
                'user' => auth()->user()?->id ?? 'system'
            ]);
            
            return (new BarangResource($barang))
                ->additional([
                    'success' => true,
                    'message' => 'Barang berhasil dibuat'
                ])
                ->response()
                ->setStatusCode(201);
            
        } catch (\Exception $e) {
            DB::rollback();
            
            Log::error('Failed to create barang', [
                'kode_divisi' => $kodeDivisi,
                'error' => $e->getMessage(),
                'data' => $request->validated()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat barang',
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Display the specified barang
     */
    public function show(string $kodeDivisi, string $kodeBarang): JsonResponse
    {
        try {
            $barang = Barang::with([
                    'divisi',
                    'kategori' => function ($q) use ($kodeDivisi) {
                        $q->where('kode_divisi', $kodeDivisi);
                    },
                    'detailBarang'
                ])
                ->withCount(['kartuStoks', 'invoiceDetails'])
                ->where('kode_divisi', $kodeDivisi)
                ->where('kode_barang', $kodeBarang)
                ->firstOrFail();

            // Get additional stock information if kartu_stok table exists
            try {
                $stockInfo = DB::selectOne("
                    SELECT 
                        COALESCE(SUM(CASE WHEN jenis = 'M' THEN qty ELSE -qty END), 0) as stok_current,
                        COUNT(*) as movement_count,
                        MAX(tanggal) as last_movement
                    FROM kartu_stok 
                    WHERE kode_divisi = ? AND kode_barang = ?
                ", [$kodeDivisi, $kodeBarang]);

                $barang->stock_info = $stockInfo;
            } catch (\Exception $e) {
                // If kartu_stok table doesn't exist, set default values
                $barang->stock_info = (object) [
                    'stok_current' => 0,
                    'movement_count' => 0,
                    'last_movement' => null
                ];
            }

            return (new BarangResource($barang))
                ->additional(['success' => true])
                ->response()
                ->setStatusCode(200);

        } catch (\Exception $e) {
            Log::error('Error fetching barang details', [
                'kode_divisi' => $kodeDivisi,
                'kode_barang' => $kodeBarang,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Barang tidak ditemukan',
                'error' => app()->environment('local') ? $e->getMessage() : 'Not found'
            ], 404);
        }
    }

    /**
     * Update the specified barang
     */
    public function update(UpdateBarangRequest $request, string $kodeDivisi, string $kodeBarang): JsonResponse
    {
        try {
            $barang = Barang::where('kode_divisi', $kodeDivisi)
                ->where('kode_barang', $kodeBarang)
                ->firstOrFail();

            $validated = $request->validated();

            DB::beginTransaction();
            
            $oldData = $barang->toArray();
            $barang->update($validated);
            $barang->load([
                'divisi',
                'kategori' => function ($q) use ($kodeDivisi) {
                    $q->where('kode_divisi', $kodeDivisi);
                }
            ]);
            
            DB::commit();
            
            Log::info('Barang updated successfully', [
                'kode_divisi' => $kodeDivisi,
                'kode_barang' => $kodeBarang,
                'old_data' => $oldData,
                'new_data' => $validated,
                'user' => auth()->user()?->id ?? 'system'
            ]);
            
            return (new BarangResource($barang))
                ->additional([
                    'success' => true,
                    'message' => 'Barang berhasil diupdate'
                ])
                ->response()
                ->setStatusCode(200);
            
        } catch (\Exception $e) {
            DB::rollback();
            
            Log::error('Failed to update barang', [
                'kode_divisi' => $kodeDivisi,
                'kode_barang' => $kodeBarang,
                'error' => $e->getMessage(),
                'data' => $request->validated()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate barang',
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Remove the specified barang (soft delete by status)
     */
    public function destroy(string $kodeDivisi, string $kodeBarang): JsonResponse
    {
        try {
            $barang = Barang::where('kode_divisi', $kodeDivisi)
                ->where('kode_barang', $kodeBarang)
                ->firstOrFail();

            // Check if barang has transactions
            $hasTransactions = false;
            try {
                $hasTransactions = DB::table('kartu_stok')
                    ->where('kode_divisi', $kodeDivisi)
                    ->where('kode_barang', $kodeBarang)
                    ->exists();
            } catch (\Exception $e) {
                // If kartu_stok table doesn't exist, proceed with hard delete
            }

            DB::beginTransaction();

            if ($hasTransactions) {
                // Soft delete by setting status to false
                $barang->update(['status' => false]);
                $barang->load([
                    'divisi',
                    'kategori' => function ($q) use ($kodeDivisi) {
                        $q->where('kode_divisi', $kodeDivisi);
                    }
                ]);
                
                DB::commit();
                
                Log::info('Barang soft deleted (has transactions)', [
                    'kode_divisi' => $kodeDivisi,
                    'kode_barang' => $kodeBarang,
                    'user' => auth()->user()?->id ?? 'system'
                ]);
                
                return (new BarangResource($barang))
                    ->additional([
                        'success' => true,
                        'message' => 'Barang berhasil dinonaktifkan (memiliki transaksi)',
                        'action' => 'soft_delete'
                    ])
                    ->response()
                    ->setStatusCode(200);
            } else {
                // Hard delete if no transactions
                $deletedData = $barang->toArray();
                $barang->delete();
                
                DB::commit();
                
                Log::info('Barang hard deleted (no transactions)', [
                    'kode_divisi' => $kodeDivisi,
                    'kode_barang' => $kodeBarang,
                    'deleted_data' => $deletedData,
                    'user' => auth()->user()?->id ?? 'system'
                ]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Barang berhasil dihapus',
                    'action' => 'hard_delete'
                ], 200);
            }
                
        } catch (\Exception $e) {
            DB::rollback();
            
            Log::error('Failed to delete barang', [
                'kode_divisi' => $kodeDivisi,
                'kode_barang' => $kodeBarang,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus barang',
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get barang stock information
     */
    public function getStock(string $kodeDivisi, string $kodeBarang): JsonResponse
    {
        $barang = Barang::where('kode_divisi', $kodeDivisi)
            ->where('kode_barang', $kodeBarang)
            ->firstOrFail();

        // Get current stock and movements
        $stockData = DB::select("
            SELECT 
                COALESCE(SUM(CASE WHEN jenis = 'M' THEN qty ELSE -qty END), 0) as stok_current,
                COUNT(*) as total_movements,
                MAX(tanggal) as last_movement
            FROM kartu_stok 
            WHERE kode_divisi = ? AND kode_barang = ?
        ", [$kodeDivisi, $kodeBarang]);

        // Get recent movements
        $recentMovements = DB::table('kartu_stok')
            ->where('kode_divisi', $kodeDivisi)
            ->where('kode_barang', $kodeBarang)
            ->orderBy('tanggal', 'desc')
            ->orderBy('urut', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'barang' => $barang,
                'stok_current' => $stockData[0]->stok_current ?? 0,
                'stok_minimum' => $barang->stok_min ?? 0,
                'is_low_stock' => ($stockData[0]->stok_current ?? 0) <= ($barang->stok_min ?? 0),
                'total_movements' => $stockData[0]->total_movements ?? 0,
                'last_movement' => $stockData[0]->last_movement,
                'recent_movements' => $recentMovements
            ]
        ]);
    }

    /**
     * Get categories for dropdown
     */
    public function getCategories(string $kodeDivisi): JsonResponse
    {
        $categories = Kategori::where('kode_divisi', $kodeDivisi)
            ->orderBy('kategori')
            ->get(['kode_kategori', 'kategori']);

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }
}
