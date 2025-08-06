<?php

namespace App\Http\Controllers;

use App\Models\MCust;
use App\Models\Invoice;
use App\Models\ReturnSales;
use Illuminate\Http\Request;

class CustomerController extends Controller
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
    public function show(MCust $mCust)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MCust $mCust)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MCust $mCust)
    {
        //
    }

    public function getNamaCust(Request $request)
    {
        $kodedivisi = $request->input('kodedivisi');
        $noref = $request->input('noref');
        $tipe = $request->input('tipe');

        $nama = null;

        if ($tipe === 'penjualan') {
            $invoice = Invoice::where('KodeDivisi', $kodedivisi)->where('NoInvoice', $noref)->first();
            if ($invoice) {
                $customer = MCust::where('KodeDivisi', $kodedivisi)->where('KodeCust', $invoice->KodeCust)->first();
                if ($customer) {
                    $nama = $customer->NamaCust;
                }
            }
        } elseif ($tipe === 'Retur Penjualan') {
            $returnSales = ReturnSales::where('KodeDivisi', $kodedivisi)->where('NoRetur', $noref)->first();
            if ($returnSales) {
                $customer = MCust::where('KodeDivisi', $kodedivisi)->where('KodeCust', $returnSales->KodeCust)->first();
                if ($customer) {
                    $nama = $customer->NamaCust;
                }
            }
        }

        return response()->json(['nama' => $nama]);
    }
}
