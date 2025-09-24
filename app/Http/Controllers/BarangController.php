<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBarangRequest;
use App\Http\Requests\UpdateBarangRequest;
use App\Http\Resources\BarangResource;
use App\Http\Resources\BarangCollection;
use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BarangController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Barang::query();
            
            // Search functionality
            if ($request->has('search')) {
                $search = $request->get('search');
                $query->where(function($q) use ($search) {
                    $q->where('nama_barang', 'ilike', "%{$search}%")
                      ->orWhere('kode_barang', 'ilike', "%{$search}%");
                });
            }

            // Filter by kategori
            if ($request->has('kode_kategori')) {
                $query->where('kode_kategori', $request->get('kode_kategori'));
            }

            // Sorting
            $sortField = $request->get('sort', 'kode_barang');
            $sortDirection = $request->get('direction', 'asc');
            $query->orderBy($sortField, $sortDirection);

            // Pagination
            $perPage = $request->get('per_page', 15);
            $barangs = $query->paginate($perPage);

            return response()->json(new BarangCollection($barangs));
        } catch (\Exception $e) {
            Log::error('Error fetching barangs: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error fetching barangs',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBarangRequest $request): JsonResponse
    {
        try {
            $barang = Barang::create($request->validated());
            
            return response()->json([
                'message' => 'Barang created successfully',
                'data' => new BarangResource($barang)
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error creating barang: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error creating barang',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $kodeBarang): JsonResponse
    {
        try {
            $barang = Barang::findOrFail($kodeBarang);
            
            return response()->json([
                'data' => new BarangResource($barang)
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching barang: ' . $e->getMessage());
            return response()->json([
                'message' => 'Barang not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBarangRequest $request, string $kodeBarang): JsonResponse
    {
        try {
            $barang = Barang::findOrFail($kodeBarang);
            $barang->update($request->validated());
            
            return response()->json([
                'message' => 'Barang updated successfully',
                'data' => new BarangResource($barang)
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating barang: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error updating barang',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $kodeBarang): JsonResponse
    {
        try {
            $barang = Barang::findOrFail($kodeBarang);
            $barang->delete();
            
            return response()->json([
                'message' => 'Barang deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting barang: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error deleting barang',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get stock information for a specific barang
     */
    public function getStockInfo(string $kodeBarang): JsonResponse
    {
        try {
            $barang = Barang::findOrFail($kodeBarang);
            
            // Get stock info from kartu_stok view or calculate
            $stockInfo = DB::select("
                SELECT 
                    COALESCE(SUM(stok_masuk), 0) as total_masuk,
                    COALESCE(SUM(stok_keluar), 0) as total_keluar,
                    COALESCE(SUM(stok_masuk) - SUM(stok_keluar), 0) as stok_akhir
                FROM v_kartu_stok 
                WHERE kode_barang = ?
            ", [$kodeBarang]);

            return response()->json([
                'data' => [
                    'barang' => new BarangResource($barang),
                    'stock_info' => $stockInfo[0] ?? [
                        'total_masuk' => 0,
                        'total_keluar' => 0,
                        'stok_akhir' => 0
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching stock info: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error fetching stock info',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get categories for barang filtering
     */
    public function getCategories(): JsonResponse
    {
        try {
            $categories = DB::select("
                SELECT DISTINCT k.kode_kategori, k.nama_kategori 
                FROM m_kategori k
                INNER JOIN m_barang b ON k.kode_kategori = b.kode_kategori
                ORDER BY k.nama_kategori
            ");

            return response()->json([
                'data' => $categories
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching categories: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error fetching categories',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
