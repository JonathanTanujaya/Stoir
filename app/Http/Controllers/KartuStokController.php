<?php

namespace App\Http\Controllers;

use App\Models\KartuStok;
use App\Models\MBarang;
use Illuminate\Http\Request;

class KartuStokController extends Controller
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
    public function show(KartuStok $kartuStok)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, KartuStok $kartuStok)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(KartuStok $kartuStok)
    {
        //
    }

    public function getVKartuStok()
    {
        $vKartuStok = KartuStok::join('m_barang', function ($join) {
            $join->on('kartu_stok.KodeBarang', '=', 'm_barang.KodeBarang')
                 ->on('kartu_stok.KodeDivisi', '=', 'm_barang.KodeDivisi');
        })
        ->select(
            'kartu_stok.urut',
            'kartu_stok.KodeDivisi',
            'kartu_stok.KodeBarang',
            'm_barang.NamaBarang',
            'm_barang.KodeKategori',
            'kartu_stok.No_Ref',
            'kartu_stok.TglProses',
            'kartu_stok.Tipe',
            'kartu_stok.Increase',
            'kartu_stok.Decrease',
            'kartu_stok.Harga_Debet',
            'kartu_stok.Harga_Kredit',
            'kartu_stok.Qty',
            'kartu_stok.HPP'
        )
        ->get();

        return response()->json($vKartuStok);
    }
}
