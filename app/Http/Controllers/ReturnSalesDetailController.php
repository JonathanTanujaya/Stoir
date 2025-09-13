<?php

namespace App\Http\Controllers;

use App\Models\ReturnSalesDetail;
use App\Models\ReturnSales;
use App\Http\Requests\StoreReturnSalesDetailRequest;
use App\Http\Requests\UpdateReturnSalesDetailRequest;
use App\Http\Resources\ReturnSalesDetailResource;
use App\Http\Resources\ReturnSalesDetailCollection;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\Builder;

class ReturnSalesDetailController extends Controller
{
    /**
     * Display a listing of the return sales details.
     */
    public function index(Request $request, string $kodeDivisi, string $noRetur): JsonResponse
    {
        // Verify parent ReturnSales exists
        $returnSales = ReturnSales::where('kode_divisi', $kodeDivisi)
            ->where('no_return_sales', $noRetur)
            ->firstOrFail();

        $query = ReturnSalesDetail::query()
            ->where('kode_divisi', $kodeDivisi)
            ->where('no_retur', $noRetur)
            ->with(['returnSales', 'barang', 'invoice']);

        // Apply filters
        if ($request->filled('kode_barang')) {
            $query->where('kode_barang', 'like', '%' . $request->kode_barang . '%');
        }

        if ($request->filled('no_invoice')) {
            $query->where('no_invoice', 'like', '%' . $request->no_invoice . '%');
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

        // Sorting
        $sortBy = $request->get('sort_by', 'id');
        $sortDirection = $request->get('sort_direction', 'asc');
        
        $allowedSorts = ['id', 'kode_barang', 'qty_retur', 'harga_nett', 'status', 'no_invoice'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortDirection);
        }

        // Pagination
        $perPage = min($request->get('per_page', 15), 100);
        $returnSalesDetails = $query->paginate($perPage);

        return response()->json(new ReturnSalesDetailCollection($returnSalesDetails));
    }

    /**
     * Store a newly created return sales detail.
     */
    public function store(StoreReturnSalesDetailRequest $request, string $kodeDivisi, string $noRetur): JsonResponse
    {
        // Verify parent ReturnSales exists
        $returnSales = ReturnSales::where('kode_divisi', $kodeDivisi)
            ->where('no_return_sales', $noRetur)
            ->firstOrFail();

        $validatedData = $request->validated();
        
        // Set default status if not provided
        if (!isset($validatedData['status'])) {
            $validatedData['status'] = 'Open';
        }

        // Check for duplicate entry
        $existingDetail = ReturnSalesDetail::where([
            'kode_divisi' => $kodeDivisi,
            'no_retur' => $noRetur,
            'kode_barang' => $validatedData['kode_barang'],
            'no_invoice' => $validatedData['no_invoice'],
        ])->first();

        if ($existingDetail) {
            return response()->json([
                'message' => 'Detail retur untuk kombinasi kode barang dan nomor invoice ini sudah ada.',
                'errors' => [
                    'kode_barang' => ['Detail dengan kode barang dan nomor invoice ini sudah ada.']
                ]
            ], 422);
        }

        $returnSalesDetail = ReturnSalesDetail::create($validatedData);
        $returnSalesDetail->load(['returnSales', 'barang', 'invoice']);

        return response()->json([
            'message' => 'Detail retur berhasil ditambahkan.',
            'data' => new ReturnSalesDetailResource($returnSalesDetail)
        ], 201);
    }

    /**
     * Display the specified return sales detail.
     */
    public function show(string $kodeDivisi, string $noRetur, ReturnSalesDetail $detail): JsonResponse
    {
        // Verify the detail belongs to the correct divisi and return sales
        if ($detail->kode_divisi !== $kodeDivisi || $detail->no_retur !== $noRetur) {
            return response()->json([
                'message' => 'Detail retur tidak ditemukan dalam retur sales ini.'
            ], 404);
        }

        $detail->load(['returnSales', 'barang', 'invoice']);

        return response()->json([
            'data' => new ReturnSalesDetailResource($detail)
        ]);
    }

    /**
     * Update the specified return sales detail.
     */
    public function update(UpdateReturnSalesDetailRequest $request, string $kodeDivisi, string $noRetur, ReturnSalesDetail $detail): JsonResponse
    {
        // Verify the detail belongs to the correct divisi and return sales
        if ($detail->kode_divisi !== $kodeDivisi || $detail->no_retur !== $noRetur) {
            return response()->json([
                'message' => 'Detail retur tidak ditemukan dalam retur sales ini.'
            ], 404);
        }

        $validatedData = $request->validated();

        // Check for duplicate if kode_barang or no_invoice is being changed
        if (isset($validatedData['kode_barang']) || isset($validatedData['no_invoice'])) {
            $checkKodeBarang = $validatedData['kode_barang'] ?? $detail->kode_barang;
            $checkNoInvoice = $validatedData['no_invoice'] ?? $detail->no_invoice;
            
            $existingDetail = ReturnSalesDetail::where([
                'kode_divisi' => $kodeDivisi,
                'no_retur' => $noRetur,
                'kode_barang' => $checkKodeBarang,
                'no_invoice' => $checkNoInvoice,
            ])->where('id', '!=', $detail->id)->first();

            if ($existingDetail) {
                return response()->json([
                    'message' => 'Detail retur untuk kombinasi kode barang dan nomor invoice ini sudah ada.',
                    'errors' => [
                        'kode_barang' => ['Detail dengan kode barang dan nomor invoice ini sudah ada.']
                    ]
                ], 422);
            }
        }

        $detail->update($validatedData);
        $detail->load(['returnSales', 'barang', 'invoice']);

        return response()->json([
            'message' => 'Detail retur berhasil diperbarui.',
            'data' => new ReturnSalesDetailResource($detail)
        ]);
    }

    /**
     * Remove the specified return sales detail.
     */
    public function destroy(string $kodeDivisi, string $noRetur, ReturnSalesDetail $detail): JsonResponse
    {
        // Verify the detail belongs to the correct divisi and return sales
        if ($detail->kode_divisi !== $kodeDivisi || $detail->no_retur !== $noRetur) {
            return response()->json([
                'message' => 'Detail retur tidak ditemukan dalam retur sales ini.'
            ], 404);
        }

        $detail->delete();

        return response()->json([
            'message' => 'Detail retur berhasil dihapus.'
        ]);
    }

    /**
     * Get statistics for return sales details.
     */
    public function stats(Request $request, string $kodeDivisi, string $noRetur): JsonResponse
    {
        // Verify parent ReturnSales exists
        $returnSales = ReturnSales::where('kode_divisi', $kodeDivisi)
            ->where('no_return_sales', $noRetur)
            ->firstOrFail();

        $query = ReturnSalesDetail::where('kode_divisi', $kodeDivisi)
            ->where('no_retur', $noRetur);

        $stats = [
            'total_details' => $query->count(),
            'total_qty_retur' => $query->sum('qty_retur'),
            'total_amount' => $query->get()->sum(function ($item) {
                return $item->qty_retur * $item->harga_nett;
            }),
            'status_breakdown' => $query->selectRaw('status, COUNT(*) as count, SUM(qty_retur) as total_qty')
                ->groupBy('status')
                ->get()
                ->mapWithKeys(function ($item) {
                    return [$item->status => [
                        'count' => $item->count,
                        'total_qty' => $item->total_qty
                    ]];
                }),
            'unique_barang' => $query->distinct('kode_barang')->count(),
            'unique_invoices' => $query->distinct('no_invoice')->count(),
            'avg_qty_per_detail' => $query->count() > 0 ? round($query->avg('qty_retur'), 2) : 0,
            'avg_price_per_unit' => $query->count() > 0 ? round($query->avg('harga_nett'), 2) : 0,
        ];

        return response()->json([
            'stats' => $stats,
            'return_sales_info' => [
                'kode_divisi' => $returnSales->kode_divisi,
                'no_return_sales' => $returnSales->no_return_sales,
                'tgl_return' => $returnSales->tgl_return,
                'kode_cust' => $returnSales->kode_cust,
                'nilai' => $returnSales->nilai,
            ]
        ]);
    }
}
