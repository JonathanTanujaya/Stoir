<?php

namespace App\Http\Controllers;

use App\Models\MBarang;
use App\Models\DBarang;
use App\Services\ProductService;
use Illuminate\Http\Request;

class BarangController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

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
    public function show(MBarang $mBarang)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MBarang $mBarang)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MBarang $mBarang)
    {
        //
    }

    public function getVBarang()
    {
        $vBarang = MBarang::leftJoin('d_barang', function ($join) {
            $join->on('m_barang.KodeDivisi', '=', 'd_barang.KodeDivisi')
                 ->on('m_barang.KodeBarang', '=', 'd_barang.KodeBarang');
        })
        ->select(
            'm_barang.KodeDivisi',
            'm_barang.KodeBarang',
            'm_barang.NamaBarang',
            'm_barang.KodeKategori',
            'm_barang.HargaList',
            'm_barang.HargaJual',
            'd_barang.TglMasuk',
            'd_barang.Modal',
            'd_barang.Stok',
            DB::raw('COALESCE(('.$this->productService->getStokClaimQuery().'), 0) AS StokClaim'),
            'm_barang.Satuan',
            'm_barang.merk',
            'm_barang.Disc1',
            'm_barang.Disc2',
            'm_barang.Barcode',
            'm_barang.status',
            'd_barang.ID',
            'm_barang.Lokasi',
            'm_barang.HargaList2',
            'm_barang.HargaJual2',
            'm_barang.Checklist',
            'm_barang.StokMin'
        )
        ->get();

        return response()->json($vBarang);
    }
}
