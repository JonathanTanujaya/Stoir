<?php

namespace App\Http\Controllers;

use App\Models\PartPenerimaan;
use App\Models\MSupplier;
use App\Models\PartPenerimaanDetail;
use App\Models\MBarang;
use Illuminate\Http\Request;

class PartPenerimaanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $partPenerimaans = PartPenerimaan::orderBy('tglpenerimaan', 'desc')
                                           ->limit(10)
                                           ->get();
            
            return response()->json([
                'success' => true,
                'message' => 'Data part penerimaan retrieved successfully',
                'data' => $partPenerimaans,
                'total_shown' => $partPenerimaans->count(),
                'note' => 'Showing latest 10 records for testing purposes'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve part penerimaan data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all part penerimaan for frontend (unlimited)
     */
    public function getAllForFrontend()
    {
        try {
            $partPenerimaans = PartPenerimaan::orderBy('tglpenerimaan', 'desc')
                                           ->get();
            
            return response()->json([
                'success' => true,
                'message' => 'All part penerimaan data retrieved successfully',
                'data' => $partPenerimaans,
                'total_records' => $partPenerimaans->count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve all part penerimaan data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'kodedivisi' => 'required|string|max:2',
                'nopenerimaan' => 'required|string|max:20',
                'tglpenerimaan' => 'required|date',
                'kodesupplier' => 'required|string|max:10',
                'details' => 'required|array',
                'details.*.kodebarang' => 'required|string|max:20',
                'details.*.qty' => 'required|numeric|min:0',
                'details.*.harga' => 'required|numeric|min:0'
            ]);

            $partPenerimaan = PartPenerimaan::create($request->only([
                'kodedivisi', 'nopenerimaan', 'tglpenerimaan', 'kodesupplier'
            ]));

            // Create details
            foreach ($request->details as $detail) {
                PartPenerimaanDetail::create([
                    'kodedivisi' => $request->kodedivisi,
                    'nopenerimaan' => $request->nopenerimaan,
                    'kodebarang' => $detail['kodebarang'],
                    'qty' => $detail['qty'],
                    'harga' => $detail['harga']
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Part penerimaan created successfully',
                'data' => $partPenerimaan->load('details')
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create part penerimaan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($kodeDivisi, $noPenerimaan)
    {
        try {
            $partPenerimaan = PartPenerimaan::with(['supplier', 'details.barang'])
                                          ->where('kodedivisi', $kodeDivisi)
                                          ->where('nopenerimaan', $noPenerimaan)
                                          ->first();
            
            if (!$partPenerimaan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Part penerimaan not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Part penerimaan retrieved successfully',
                'data' => $partPenerimaan
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve part penerimaan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $kodeDivisi, $noPenerimaan)
    {
        try {
            $partPenerimaan = PartPenerimaan::where('kodedivisi', $kodeDivisi)
                                          ->where('nopenerimaan', $noPenerimaan)
                                          ->first();
            
            if (!$partPenerimaan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Part penerimaan not found'
                ], 404);
            }

            $request->validate([
                'tglpenerimaan' => 'required|date',
                'kodesupplier' => 'required|string|max:10'
            ]);

            $partPenerimaan->update($request->only(['tglpenerimaan', 'kodesupplier']));

            return response()->json([
                'success' => true,
                'message' => 'Part penerimaan updated successfully',
                'data' => $partPenerimaan
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update part penerimaan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($kodeDivisi, $noPenerimaan)
    {
        try {
            $partPenerimaan = PartPenerimaan::where('kodedivisi', $kodeDivisi)
                                          ->where('nopenerimaan', $noPenerimaan)
                                          ->first();
            
            if (!$partPenerimaan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Part penerimaan not found'
                ], 404);
            }

            // Delete details first
            PartPenerimaanDetail::where('kodedivisi', $kodeDivisi)
                               ->where('nopenerimaan', $noPenerimaan)
                               ->delete();

            $partPenerimaan->delete();

            return response()->json([
                'success' => true,
                'message' => 'Part penerimaan deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete part penerimaan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getVPartPenerimaan()
    {
        $vPartPenerimaan = MBarang::rightJoin('part_penerimaan_detail', function ($join) {
            $join->on('m_barang.KodeBarang', '=', 'part_penerimaan_detail.KodeBarang')
                 ->on('m_barang.KodeDivisi', '=', 'part_penerimaan_detail.KodeDivisi');
        })
        ->leftJoin('part_penerimaan', function ($join) {
            $join->on('part_penerimaan_detail.KodeDivisi', '=', 'part_penerimaan.KodeDivisi')
                 ->on('part_penerimaan_detail.NoPenerimaan', '=', 'part_penerimaan.NoPenerimaan');
        })
        ->leftJoin('m_supplier', function ($join) {
            $join->on('part_penerimaan.KodeDivisi', '=', 'm_supplier.KodeDivisi')
                 ->on('part_penerimaan.KodeSupplier', '=', 'm_supplier.KodeSupplier');
        })
        ->select(
            'part_penerimaan.KodeDivisi',
            'part_penerimaan.NoPenerimaan',
            'part_penerimaan.TglPenerimaan',
            'part_penerimaan.KodeSupplier',
            'm_supplier.NamaSupplier',
            'part_penerimaan.JatuhTempo',
            'part_penerimaan.NoFaktur',
            'part_penerimaan.Total',
            'part_penerimaan.Discount',
            'part_penerimaan.Pajak',
            'part_penerimaan.GrandTotal',
            'part_penerimaan.Status',
            'part_penerimaan_detail.KodeBarang',
            'part_penerimaan_detail.QtySupply',
            'part_penerimaan_detail.Harga',
            'part_penerimaan_detail.Diskon1',
            'part_penerimaan_detail.Diskon2',
            'part_penerimaan_detail.HargaNett',
            'm_barang.NamaBarang'
        )
        ->get();

        return response()->json($vPartPenerimaan);
    }

    public function getVPartPenerimaanHeader()
    {
        $vPartPenerimaanHeader = PartPenerimaan::join('m_supplier', function ($join) {
            $join->on('part_penerimaan.KodeDivisi', '=', 'm_supplier.KodeDivisi')
                 ->on('part_penerimaan.KodeSupplier', '=', 'm_supplier.KodeSupplier');
        })
        ->select(
            'part_penerimaan.KodeDivisi',
            'part_penerimaan.NoPenerimaan',
            'part_penerimaan.TglPenerimaan',
            'part_penerimaan.KodeSupplier',
            'm_supplier.NamaSupplier',
            'part_penerimaan.JatuhTempo',
            'part_penerimaan.NoFaktur',
            'part_penerimaan.Total',
            'part_penerimaan.Discount',
            'part_penerimaan.Pajak',
            'part_penerimaan.GrandTotal',
            'part_penerimaan.Status'
        )
        ->get();

        return response()->json($vPartPenerimaanHeader);
    }
}
