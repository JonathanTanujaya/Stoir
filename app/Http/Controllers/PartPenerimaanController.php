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
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(PartPenerimaan $partPenerimaan)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PartPenerimaan $partPenerimaan)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PartPenerimaan $partPenerimaan)
    {
        //
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
