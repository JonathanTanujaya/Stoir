<?php

namespace App\Http\Controllers;

use App\Models\MTrans;
use App\Models\DTrans;
use App\Models\MCOA;
use Illuminate\Http\Request;

class MTransController extends Controller
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
    public function show(MTrans $mTrans)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MTrans $mTrans)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MTrans $mTrans)
    {
        //
    }

    public function getVTrans()
    {
        $vTrans = DTrans::join('m_trans', 'd_trans.KodeTrans', '=', 'm_trans.KodeTrans')
            ->join('m_coa', 'd_trans.KodeCOA', '=', 'm_coa.KodeCOA')
            ->select(
                'd_trans.KodeTrans',
                'd_trans.KodeCOA',
                'd_trans.SaldoNormal',
                'm_trans.Transaksi',
                'm_coa.NamaCOA'
            )
            ->get();

        return response()->json($vTrans);
    }
}
