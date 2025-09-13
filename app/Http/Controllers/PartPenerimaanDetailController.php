<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePartPenerimaanDetailRequest;
use App\Http\Resources\PartPenerimaanDetailCollection;
use App\Http\Resources\PartPenerimaanDetailResource;
use App\Models\PartPenerimaan;
use App\Models\PartPenerimaanDetail;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class PartPenerimaanDetailController extends Controller
{
    /**
     * Display a listing of part penerimaan details for a specific penerimaan.
     * 
     * Note: This model does not have a single primary key defined in the database schema,
     * therefore individual show(), update(), and destroy() methods are not implemented
     * as standard resource methods.
     */
    public function index(Request $request, string $kodeDivisi, string $noPenerimaan): Response
    {
        // Verify that the parent part penerimaan exists
        $partPenerimaan = PartPenerimaan::where([
            'kode_divisi' => $kodeDivisi,
            'no_penerimaan' => $noPenerimaan
        ])->firstOrFail();

        $query = PartPenerimaanDetail::where([
            'kode_divisi' => $kodeDivisi,
            'no_penerimaan' => $noPenerimaan
        ]);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
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
        $query->with(['partPenerimaan', 'barang', 'divisi']);

        // Pagination
        $perPage = min($request->get('per_page', 15), 100);
        $details = $query->paginate($perPage);

        return response(new PartPenerimaanDetailCollection($details));
    }

    /**
     * Store a newly created part penerimaan detail in storage.
     * 
     * Note: Due to the lack of a single primary key in the database schema,
     * updates and deletions of individual details must be handled through
     * bulk operations or by recreating the entire detail set.
     */
    public function store(StorePartPenerimaanDetailRequest $request, string $kodeDivisi, string $noPenerimaan): Response
    {
        // Verify that the parent part penerimaan exists
        $partPenerimaan = PartPenerimaan::where([
            'kode_divisi' => $kodeDivisi,
            'no_penerimaan' => $noPenerimaan
        ])->firstOrFail();

        $validated = $request->validated();
        $validated['kode_divisi'] = $kodeDivisi;
        $validated['no_penerimaan'] = $noPenerimaan;

        // Check if detail with same kode_barang already exists
        $existingDetail = PartPenerimaanDetail::where([
            'kode_divisi' => $kodeDivisi,
            'no_penerimaan' => $noPenerimaan,
            'kode_barang' => $validated['kode_barang']
        ])->first();

        if ($existingDetail) {
            return response([
                'message' => 'Detail dengan kode barang ini sudah ada untuk penerimaan ini.',
                'error' => 'duplicate_detail'
            ], 422);
        }

        $detail = PartPenerimaanDetail::create($validated);
        
        // Load relationships for response
        $detail->load(['partPenerimaan', 'barang', 'divisi']);

        return response(new PartPenerimaanDetailResource($detail), 201);
    }

    /**
     * Get statistics for part penerimaan details.
     */
    public function stats(string $kodeDivisi, string $noPenerimaan): Response
    {
        // Verify that the parent part penerimaan exists
        $partPenerimaan = PartPenerimaan::where([
            'kode_divisi' => $kodeDivisi,
            'no_penerimaan' => $noPenerimaan
        ])->firstOrFail();

        $stats = [
            'total_items' => PartPenerimaanDetail::where([
                'kode_divisi' => $kodeDivisi,
                'no_penerimaan' => $noPenerimaan
            ])->count(),
            
            'total_quantity' => PartPenerimaanDetail::where([
                'kode_divisi' => $kodeDivisi,
                'no_penerimaan' => $noPenerimaan
            ])->sum('qty_supply'),
            
            'total_gross_amount' => PartPenerimaanDetail::where([
                'kode_divisi' => $kodeDivisi,
                'no_penerimaan' => $noPenerimaan
            ])->sum(DB::raw('qty_supply * harga')),
            
            'total_net_amount' => PartPenerimaanDetail::where([
                'kode_divisi' => $kodeDivisi,
                'no_penerimaan' => $noPenerimaan
            ])->sum('harga_nett'),
            
            'average_unit_price' => PartPenerimaanDetail::where([
                'kode_divisi' => $kodeDivisi,
                'no_penerimaan' => $noPenerimaan
            ])->avg('harga'),
            
            'total_discount_amount' => PartPenerimaanDetail::where([
                'kode_divisi' => $kodeDivisi,
                'no_penerimaan' => $noPenerimaan
            ])->sum(DB::raw('(qty_supply * harga) - harga_nett'))
        ];

        return response($stats);
    }

    /**
     * Bulk delete all details for a specific part penerimaan.
     * 
     * This method is provided as an alternative to individual deletions
     * since the table lacks a single primary key.
     */
    public function bulkDelete(string $kodeDivisi, string $noPenerimaan): Response
    {
        // Verify that the parent part penerimaan exists
        $partPenerimaan = PartPenerimaan::where([
            'kode_divisi' => $kodeDivisi,
            'no_penerimaan' => $noPenerimaan
        ])->firstOrFail();

        $deletedCount = PartPenerimaanDetail::where([
            'kode_divisi' => $kodeDivisi,
            'no_penerimaan' => $noPenerimaan
        ])->delete();

        return response([
            'message' => "Berhasil menghapus {$deletedCount} detail penerimaan.",
            'deleted_count' => $deletedCount
        ]);
    }
}
