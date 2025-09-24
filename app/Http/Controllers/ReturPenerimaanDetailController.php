<?php

namespace App\Http\Controllers;

use App\Models\ReturPenerimaanDetail;
use App\Models\ReturPenerimaan;
use App\Http\Requests\StoreReturPenerimaanDetailRequest;
use App\Http\Requests\UpdateReturPenerimaanDetailRequest;
use App\Http\Resources\ReturPenerimaanDetailResource;
use App\Http\Resources\ReturPenerimaanDetailCollection;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ReturPenerimaanDetailController extends Controller
{
    /**
     * Display a listing of the retur penerimaan details.
     */
    public function index(Request $request, string $noRetur): JsonResponse
    {
        // Verify parent ReturPenerimaan exists
        $returPenerimaan = ReturPenerimaan::where('no_retur_penerimaan', $noRetur)
            ->firstOrFail();

        $query = ReturPenerimaanDetail::where('no_retur', $noRetur)
            ->with(['returPenerimaan', 'barang', 'partPenerimaan']);

        // Apply filters
        if ($request->filled('kode_barang')) {
            $query->where('kode_barang', 'ILIKE', '%' . $request->kode_barang . '%');
        }

        if ($request->filled('no_penerimaan')) {
            $query->where('no_penerimaan', 'ILIKE', '%' . $request->no_penerimaan . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('min_qty')) {
            $query->where('qty_retur', '>=', $request->min_qty);
        }

        if ($request->filled('max_qty')) {
            $query->where('qty_retur', '<=', $request->max_qty);
        }

        if ($request->filled('min_harga')) {
            $query->where('harga_nett', '>=', $request->min_harga);
        }

        if ($request->filled('max_harga')) {
            $query->where('harga_nett', '<=', $request->max_harga);
        }

        // Apply sorting
        $sortBy = $request->get('sort_by', 'id');
        $sortDirection = $request->get('sort_direction', 'asc');
        
        $allowedSortFields = ['id', 'kode_barang', 'qty_retur', 'harga_nett', 'status', 'no_penerimaan'];
        if (in_array($sortBy, $allowedSortFields)) {
            $query->orderBy($sortBy, $sortDirection);
        }

        // Pagination
        $perPage = min($request->get('per_page', 15), 100);
        $details = $query->paginate($perPage);

        return response()->json(new ReturPenerimaanDetailCollection($details));
    }

    /**
     * Store a newly created retur penerimaan detail.
     */
    public function store(StoreReturPenerimaanDetailRequest $request, string $noRetur): JsonResponse
    {
        // Verify parent ReturPenerimaan exists
        $returPenerimaan = ReturPenerimaan::where('no_retur_penerimaan', $noRetur)
            ->firstOrFail();

        // Check for duplicates
        $exists = ReturPenerimaanDetail::where('no_retur', $noRetur)
            ->where('kode_barang', $request->kode_barang)
            ->where('no_penerimaan', $request->no_penerimaan)
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Detail retur penerimaan dengan kombinasi barang dan penerimaan ini sudah ada.',
                'errors' => [
                    'duplicate' => ['Kombinasi kode_barang dan no_penerimaan sudah ada dalam retur ini.']
                ]
            ], 422);
        }

        $validated = $request->validated();
        $validated['status'] = $validated['status'] ?? 'Open';

        $detail = ReturPenerimaanDetail::create($validated);
        $detail->load(['returPenerimaan', 'barang', 'partPenerimaan']);

        return response()->json([
            'message' => 'Detail retur penerimaan berhasil dibuat.',
            'data' => new ReturPenerimaanDetailResource($detail)
        ], 201);
    }

    /**
     * Display the specified retur penerimaan detail.
     */
    public function show(string $noRetur, string $id): JsonResponse
    {
        // Verify parent ReturPenerimaan exists
        $returPenerimaan = ReturPenerimaan::where('no_retur_penerimaan', $noRetur)
            ->firstOrFail();

        $detail = ReturPenerimaanDetail::where('no_retur', $noRetur)
            ->where('id', $id)
            ->with(['returPenerimaan', 'barang', 'partPenerimaan'])
            ->firstOrFail();

        return response()->json([
            'data' => new ReturPenerimaanDetailResource($detail)
        ]);
    }

    /**
     * Update the specified retur penerimaan detail.
     */
    public function update(UpdateReturPenerimaanDetailRequest $request, string $noRetur, string $id): JsonResponse
    {
        // Verify parent ReturPenerimaan exists
        $returPenerimaan = ReturPenerimaan::where('no_retur_penerimaan', $noRetur)
            ->firstOrFail();

        $detail = ReturPenerimaanDetail::where('no_retur', $noRetur)
            ->where('id', $id)
            ->firstOrFail();

        // Check for duplicates if kode_barang or no_penerimaan is being changed
        if ($request->has('kode_barang') || $request->has('no_penerimaan')) {
            $kodeBarang = $request->get('kode_barang', $detail->kode_barang);
            $noPenerimaan = $request->get('no_penerimaan', $detail->no_penerimaan);

            $exists = ReturPenerimaanDetail::where('no_retur', $noRetur)
                ->where('kode_barang', $kodeBarang)
                ->where('no_penerimaan', $noPenerimaan)
                ->where('id', '!=', $id)
                ->exists();

            if ($exists) {
                return response()->json([
                    'message' => 'Detail retur penerimaan dengan kombinasi barang dan penerimaan ini sudah ada.',
                    'errors' => [
                        'duplicate' => ['Kombinasi kode_barang dan no_penerimaan sudah ada dalam retur ini.']
                    ]
                ], 422);
            }
        }

        $detail->update($request->validated());
        $detail->load(['returPenerimaan', 'barang', 'partPenerimaan']);

        return response()->json([
            'message' => 'Detail retur penerimaan berhasil diperbarui.',
            'data' => new ReturPenerimaanDetailResource($detail)
        ]);
    }

    /**
     * Remove the specified retur penerimaan detail.
     */
    public function destroy(string $noRetur, string $id): JsonResponse
    {
        // Verify parent ReturPenerimaan exists
        $returPenerimaan = ReturPenerimaan::where('no_retur_penerimaan', $noRetur)
            ->firstOrFail();

        $detail = ReturPenerimaanDetail::where('no_retur', $noRetur)
            ->where('id', $id)
            ->firstOrFail();

        $detail->delete();

        return response()->json([
            'message' => 'Detail retur penerimaan berhasil dihapus.'
        ]);
    }

    /**
     * Get statistics for retur penerimaan details.
     */
    public function stats(Request $request, string $noRetur): JsonResponse
    {
        // Verify parent ReturPenerimaan exists
        $returPenerimaan = ReturPenerimaan::where('no_retur_penerimaan', $noRetur)
            ->firstOrFail();

        $query = ReturPenerimaanDetail::where('no_retur', $noRetur);

        $totalDetails = $query->count();
        $totalQtyRetur = $query->sum('qty_retur');
        $totalAmount = $query->selectRaw('SUM(qty_retur * harga_nett) as total')->value('total') ?? 0;
        
        $statusBreakdown = $query->selectRaw('status, COUNT(*) as count, SUM(qty_retur) as total_qty')
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        $uniqueBarang = $query->distinct('kode_barang')->count('kode_barang');
        $uniquePenerimaan = $query->distinct('no_penerimaan')->count('no_penerimaan');

        $avgQtyPerDetail = $totalDetails > 0 ? round($totalQtyRetur / $totalDetails, 2) : 0;
        $avgPricePerUnit = $totalQtyRetur > 0 ? round($totalAmount / $totalQtyRetur, 2) : 0;

        return response()->json([
            'retur_penerimaan' => [
                'no_retur_penerimaan' => $returPenerimaan->no_retur_penerimaan,
                'total' => $returPenerimaan->total,
                'status' => $returPenerimaan->status,
            ],
            'statistics' => [
                'total_details' => $totalDetails,
                'total_qty_retur' => $totalQtyRetur,
                'total_amount' => round($totalAmount, 2),
                'status_breakdown' => $statusBreakdown,
                'unique_barang' => $uniqueBarang,
                'unique_penerimaan' => $uniquePenerimaan,
                'avg_qty_per_detail' => $avgQtyPerDetail,
                'avg_price_per_unit' => $avgPricePerUnit,
            ]
        ]);
    }
}
