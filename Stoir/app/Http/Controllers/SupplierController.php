<?php

namespace App\Http\Controllers;

use App\Models\MSupplier;
use App\Models\PartPenerimaan;
use Illuminate\Http\Request;

class SupplierController extends Controller
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
    public function show(MSupplier $mSupplier)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MSupplier $mSupplier)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MSupplier $mSupplier)
    {
        //
    }

    public function getNamaSupplier(Request $request)
    {
        $kodedivisi = $request->input('kodedivisi');
        $noref = $request->input('noref');

        $nama = null;

        $partPenerimaan = PartPenerimaan::where('KodeDivisi', $kodedivisi)->where('NoPenerimaan', $noref)->first();
        if ($partPenerimaan) {
            $supplier = MSupplier::where('KodeDivisi', $kodedivisi)->where('KodeSupplier', $partPenerimaan->KodeSupplier)->first();
            if ($supplier) {
                $nama = $supplier->NamaSupplier;
            }
        }

        return response()->json(['nama' => $nama]);
    }
}
