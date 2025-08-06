<?php

namespace App\Http\Controllers;

use App\Models\MResi;
use App\Models\DBank;
use App\Models\MBank;
use App\Models\MCust;
use Illuminate\Http\Request;

class MResiController extends Controller
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
    public function show(MResi $mResi)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MResi $mResi)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MResi $mResi)
    {
        //
    }

    public function getVCustomerResi()
    {
        $vCustomerResi = DBank::rightJoin('m_resi', function ($join) {
            $join->on('d_bank.KodeDivisi', '=', 'm_resi.KodeDivisi')
                 ->on('d_bank.NoRekening', '=', 'm_resi.NoRekeningTujuan');
        })
        ->leftJoin('m_cust', function ($join) {
            $join->on('m_resi.KodeDivisi', '=', 'm_cust.KodeDivisi')
                 ->on('m_resi.KodeCust', '=', 'm_cust.KodeCust');
        })
        ->leftJoin('m_bank', function ($join) {
            $join->on('d_bank.KodeDivisi', '=', 'm_bank.KodeDivisi')
                 ->on('d_bank.KodeBank', '=', 'm_bank.KodeBank');
        })
        ->select(
            'm_resi.KodeDivisi',
            'm_resi.NoResi',
            'm_resi.NoRekeningTujuan',
            'm_resi.TglPembayaran',
            'm_resi.KodeCust',
            'm_cust.NamaCust',
            'm_resi.Jumlah',
            'm_resi.SisaResi',
            'm_resi.Keterangan',
            'm_resi.Status',
            'd_bank.KodeBank',
            'm_bank.NamaBank'
        )
        ->get();

        return response()->json($vCustomerResi);
    }
}
