<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePartPenerimaanDetailRequest;
use App\Http\Resources\PartPenerimaanDetailCollection;
use App\Http\Resources\PartPenerimaanDetailResource;
use App\Models\PartPenerimaan;
use App\Models\PartPenerimaanDetail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PartPenerimaanDetailController extends Controller
{
    /**
     * Display a listing of part penerimaan details for a specific penerimaan.
     */
    public function index(Request $request, string $noPenerimaan): JsonResponse
    {
        try {
            // Verify that the parent part penerimaan exists
            $partPenerimaan = PartPenerimaan::where('no_penerimaan', $noPenerimaan)->firstOrFail();

            $query = PartPenerimaanDetail::where('no_penerimaan', $noPenerimaan);

            // Search functionality
            if ($request->filled('search')) {
                $search = $request->get('search');
                $query->where(function ($q) use ($search) {
                    $q->where('kode_barang', 'ILIKE', "%{$search}%");
                });
            }

            // Filter by specific fields
            if ($request->filled('kode_barang')) {
                $query->where('kode_barang', 'ILIKE', "%{$request->get('kode_barang')}%");
            }

            // Price range filters
            if ($request->filled('min_harga')) {
                $query->where('harga', '>=', $request->get('min_harga'));
            }

            if ($request->filled('max_harga')) {
                $query->where('harga', '<=', $request->get('max_harga'));
            }

            // Quantity range filters
            if ($request->filled('min_qty')) {
                $query->where('qty_supply', '>=', $request->get('min_qty'));
            }

            if ($request->filled('max_qty')) {
                $query->where('qty_supply', '<=', $request->get('max_qty'));
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'kode_barang');
            $sortOrder = $request->get('sort_order', 'asc');

            if (in_array($sortBy, ['kode_barang', 'qty_supply', 'harga', 'harga_nett'])) {
                $query->orderBy($sortBy, $sortOrder);
            }

            // Load relationships
            $query->with(['partPenerimaan', 'barang']);

            // Pagination
            $perPage = min($request->get('per_page', 15), 100);
            $details = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => new PartPenerimaanDetailCollection($details),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data detail penerimaan part',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created part penerimaan detail in storage.
     */
    public function store(StorePartPenerimaanDetailRequest $request, string $noPenerimaan): JsonResponse
    {
        try {
            // Verify that the parent part penerimaan exists
            $partPenerimaan = PartPenerimaan::where('no_penerimaan', $noPenerimaan)->firstOrFail();

            DB::beginTransaction();

            $validated = $request->validated();
            $validated['no_penerimaan'] = $noPenerimaan;

            // Check if detail with same kode_barang already exists
            $existingDetail = PartPenerimaanDetail::where([
                'no_penerimaan' => $noPenerimaan,
                'kode_barang' => $validated['kode_barang'],
            ])->first();

            if ($existingDetail) {
                DB::rollBack();

                return response()->json([
                    'success' => false,
                    'message' => 'Detail dengan kode barang ini sudah ada untuk penerimaan ini.',
                    'error' => 'duplicate_detail',
                ], 422);
            }

            $detail = PartPenerimaanDetail::create($validated);

            // Load relationships for response
            $detail->load(['partPenerimaan', 'barang']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Detail penerimaan part berhasil dibuat',
                'data' => new PartPenerimaanDetailResource($detail),
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat detail penerimaan part',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get statistics for part penerimaan details.
     */
    public function stats(string $noPenerimaan): JsonResponse
    {
        try {
            // Verify that the parent part penerimaan exists
            $partPenerimaan = PartPenerimaan::where('no_penerimaan', $noPenerimaan)->firstOrFail();

            $stats = [
                'total_items' => PartPenerimaanDetail::where('no_penerimaan', $noPenerimaan)->count(),

                'total_quantity' => PartPenerimaanDetail::where('no_penerimaan', $noPenerimaan)->sum('qty_supply'),

                'total_gross_amount' => PartPenerimaanDetail::where('no_penerimaan', $noPenerimaan)
                    ->sum(DB::raw('qty_supply * harga')),

                'total_net_amount' => PartPenerimaanDetail::where('no_penerimaan', $noPenerimaan)->sum('harga_nett'),

                'average_unit_price' => PartPenerimaanDetail::where('no_penerimaan', $noPenerimaan)->avg('harga'),

                'total_discount_amount' => PartPenerimaanDetail::where('no_penerimaan', $noPenerimaan)
                    ->sum(DB::raw('(qty_supply * harga) - harga_nett')),
            ];

            return response()->json([
                'success' => true,
                'data' => $stats,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil statistik detail penerimaan part',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Bulk delete all details for a specific part penerimaan.
     */
    public function bulkDelete(string $noPenerimaan): JsonResponse
    {
        try {
            // Verify that the parent part penerimaan exists
            $partPenerimaan = PartPenerimaan::where('no_penerimaan', $noPenerimaan)->firstOrFail();

            DB::beginTransaction();

            $deletedCount = PartPenerimaanDetail::where('no_penerimaan', $noPenerimaan)->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Berhasil menghapus {$deletedCount} detail penerimaan.",
                'data' => ['deleted_count' => $deletedCount],
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus detail penerimaan part',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
