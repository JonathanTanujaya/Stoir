<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDBarangRequest;
use App\Http\Requests\UpdateDBarangRequest;
use App\Http\Resources\DBarangCollection;
use App\Http\Resources\DBarangResource;
use App\Models\DBarang;
use App\Models\Divisi;
use App\Models\Barang;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DBarangController extends Controller
{
    /**
     * Display a listing of barang details for a specific division and product.
     */
    public function index(Request $request, string $kodeDivisi, string $kodeBarang): JsonResponse
    {
        // Verify division exists
        $divisi = Divisi::where('kode_divisi', $kodeDivisi)->first();
        if (!$divisi) {
            return response()->json([
                'success' => false,
                'message' => 'Divisi tidak ditemukan.',
            ], 404);
        }

        // Verify barang exists
        $barang = Barang::where('kode_divisi', $kodeDivisi)
            ->where('kode_barang', $kodeBarang)
            ->first();
        if (!$barang) {
            return response()->json([
                'success' => false,
                'message' => 'Barang tidak ditemukan.',
            ], 404);
        }

        $query = DBarang::where('kode_divisi', $kodeDivisi)
            ->where('kode_barang', $kodeBarang)
            ->with(['divisi', 'barang']);

        // Filter by stock availability
        if ($request->filled('available_only')) {
            if ($request->boolean('available_only')) {
                $query->where('stok', '>', 0);
            }
        }

        // Filter by modal price range
        if ($request->filled('modal_min')) {
            $query->where('modal', '>=', $request->modal_min);
        }

        if ($request->filled('modal_max')) {
            $query->where('modal', '<=', $request->modal_max);
        }

        // Filter by stock range
        if ($request->filled('stok_min')) {
            $query->where('stok', '>=', $request->stok_min);
        }

        if ($request->filled('stok_max')) {
            $query->where('stok', '<=', $request->stok_max);
        }

        // Filter by date range
        if ($request->filled('tgl_masuk_from')) {
            $query->whereDate('tgl_masuk', '>=', $request->tgl_masuk_from);
        }

        if ($request->filled('tgl_masuk_to')) {
            $query->whereDate('tgl_masuk', '<=', $request->tgl_masuk_to);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'tgl_masuk');
        $sortOrder = $request->get('sort_order', 'desc');
        
        $allowedSorts = ['id', 'tgl_masuk', 'modal', 'stok', 'created_at'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->orderBy('tgl_masuk', 'desc');
        }

        $barangDetails = $query->get();

        return response()->json([
            'success' => true,
            'message' => 'Data detail barang berhasil diambil.',
            'data' => new DBarangCollection($barangDetails),
            'division_info' => [
                'kode_divisi' => $divisi->kode_divisi,
                'nama_divisi' => $divisi->nama_divisi ?? null,
            ],
            'product_info' => [
                'kode_barang' => $barang->kode_barang,
                'nama_barang' => $barang->nama_barang ?? null,
                'satuan' => $barang->satuan ?? null,
                'kategori' => $barang->kategori ?? null,
            ]
        ]);
    }

    /**
     * Store a newly created barang detail.
     */
    public function store(StoreDBarangRequest $request, string $kodeDivisi, string $kodeBarang): JsonResponse
    {
        // Verify division exists
        $divisi = Divisi::where('kode_divisi', $kodeDivisi)->first();
        if (!$divisi) {
            return response()->json([
                'success' => false,
                'message' => 'Divisi tidak ditemukan.',
            ], 404);
        }

        // Verify barang exists
        $barang = Barang::where('kode_divisi', $kodeDivisi)
            ->where('kode_barang', $kodeBarang)
            ->first();
        if (!$barang) {
            return response()->json([
                'success' => false,
                'message' => 'Barang tidak ditemukan.',
            ], 404);
        }

        $validatedData = $request->validated();
        
        // Set default tanggal masuk if not provided
        if (!isset($validatedData['tgl_masuk'])) {
            $validatedData['tgl_masuk'] = now();
        }

        $barangDetail = DBarang::create($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Detail barang berhasil dibuat.',
            'data' => new DBarangResource($barangDetail->load(['divisi', 'barang']))
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $kodeDivisi, string $kodeBarang, int $id): JsonResponse
    {
        $barangDetail = DBarang::where('id', $id)
            ->where('kode_divisi', $kodeDivisi)
            ->where('kode_barang', $kodeBarang)
            ->with(['divisi', 'barang', 'stockMovements'])
            ->first();

        if (!$barangDetail) {
            return response()->json([
                'success' => false,
                'message' => 'Detail barang tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail barang berhasil diambil.',
            'data' => new DBarangResource($barangDetail)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDBarangRequest $request, string $kodeDivisi, string $kodeBarang, int $id): JsonResponse
    {
        $barangDetail = DBarang::where('id', $id)
            ->where('kode_divisi', $kodeDivisi)
            ->where('kode_barang', $kodeBarang)
            ->first();

        if (!$barangDetail) {
            return response()->json([
                'success' => false,
                'message' => 'Detail barang tidak ditemukan.',
            ], 404);
        }

        $barangDetail->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Detail barang berhasil diperbarui.',
            'data' => new DBarangResource($barangDetail->load(['divisi', 'barang']))
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $kodeDivisi, string $kodeBarang, int $id): JsonResponse
    {
        $barangDetail = DBarang::where('id', $id)
            ->where('kode_divisi', $kodeDivisi)
            ->where('kode_barang', $kodeBarang)
            ->first();

        if (!$barangDetail) {
            return response()->json([
                'success' => false,
                'message' => 'Detail barang tidak ditemukan.',
            ], 404);
        }

        // Check if there are stock movements
        if ($barangDetail->stockMovements()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Detail barang tidak dapat dihapus karena memiliki riwayat pergerakan stok.',
            ], 422);
        }

        $barangDetail->delete();

        return response()->json([
            'success' => true,
            'message' => 'Detail barang berhasil dihapus.',
        ]);
    }

    /**
     * Get statistics for barang details.
     */
    public function statistics(string $kodeDivisi, string $kodeBarang): JsonResponse
    {
        // Verify division exists
        $divisi = Divisi::where('kode_divisi', $kodeDivisi)->first();
        if (!$divisi) {
            return response()->json([
                'success' => false,
                'message' => 'Divisi tidak ditemukan.',
            ], 404);
        }

        // Verify barang exists
        $barang = Barang::where('kode_divisi', $kodeDivisi)
            ->where('kode_barang', $kodeBarang)
            ->first();
        if (!$barang) {
            return response()->json([
                'success' => false,
                'message' => 'Barang tidak ditemukan.',
            ], 404);
        }

        $details = DBarang::where('kode_divisi', $kodeDivisi)
            ->where('kode_barang', $kodeBarang)
            ->get();

        $totalStock = $details->sum('stok');
        $totalValue = $details->sum(function ($item) {
            return ($item->modal ?? 0) * ($item->stok ?? 0);
        });

        $stats = [
            'total_entries' => $details->count(),
            'total_stock' => $totalStock,
            'total_inventory_value' => $totalValue,
            'average_modal' => $details->where('modal', '>', 0)->avg('modal') ?? 0,
            'highest_modal' => $details->max('modal') ?? 0,
            'lowest_modal' => $details->where('modal', '>', 0)->min('modal') ?? 0,
            'available_entries' => $details->where('stok', '>', 0)->count(),
            'empty_entries' => $details->where('stok', '=', 0)->count(),
            'stock_distribution' => [
                'high_stock' => $details->where('stok', '>', 20)->count(),
                'medium_stock' => $details->whereBetween('stok', [5, 20])->count(),
                'low_stock' => $details->whereBetween('stok', [1, 4])->count(),
                'empty_stock' => $details->where('stok', '=', 0)->count(),
            ],
            'value_distribution' => [
                'high_value' => $details->where('modal', '>', 1000000)->count(),
                'medium_value' => $details->whereBetween('modal', [100000, 1000000])->count(),
                'low_value' => $details->whereBetween('modal', [1, 99999])->count(),
                'no_value' => $details->where('modal', '<=', 0)->count(),
            ],
            'formatted_totals' => [
                'total_inventory_value' => 'Rp ' . number_format($totalValue, 2, ',', '.'),
                'average_modal' => 'Rp ' . number_format($details->where('modal', '>', 0)->avg('modal') ?? 0, 2, ',', '.'),
            ]
        ];

        return response()->json([
            'success' => true,
            'message' => 'Statistik detail barang berhasil diambil.',
            'data' => $stats,
            'division_info' => [
                'kode_divisi' => $divisi->kode_divisi,
                'nama_divisi' => $divisi->nama_divisi ?? null,
            ],
            'product_info' => [
                'kode_barang' => $barang->kode_barang,
                'nama_barang' => $barang->nama_barang ?? null,
                'satuan' => $barang->satuan ?? null,
                'kategori' => $barang->kategori ?? null,
            ]
        ]);
    }
}
