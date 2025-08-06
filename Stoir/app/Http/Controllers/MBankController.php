<?php

namespace App\Http\Controllers;

use App\Models\MBank;
use App\Models\DBank;
use Illuminate\Http\Request;

class MBankController extends Controller
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
    public function show(MBank $mBank)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MBank $mBank)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MBank $mBank)
    {
        //
    }

    public function getVBank()
    {
        $vBank = MBank::rightJoin('d_bank', function ($join) {
            $join->on('m_bank.KodeDivisi', '=', 'd_bank.KodeDivisi')
                 ->on('m_bank.KodeBank', '=', 'd_bank.KodeBank');
        })
        ->select(
            'd_bank.KodeDivisi',
            'd_bank.NoRekening',
            'd_bank.KodeBank',
            'm_bank.NamaBank',
            'd_bank.AtasNama',
            'd_bank.Status'
        )
        ->get();

        return response()->json($vBank);
    }
}
