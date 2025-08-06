<?php

namespace App\Http\Controllers;

use App\Models\MTT;
use App\Models\MCust;
use App\Models\DTT;
use App\Models\Invoice;
use App\Models\MSales;
use App\Models\ReturnSales;
use App\Models\ReturnSalesDetail;
use App\Models\MBarang;
use Illuminate\Http\Request;

class MTTController extends Controller
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
    public function show(MTT $mTT)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MTT $mTT)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MTT $mTT)
    {
        //
    }

    public function getVTT()
    {
        $vtt = MTT::leftJoin('m_cust', 'm_tt.KodeCust', '=', 'm_cust.KodeCust')
            ->select(
                'm_tt.NoTT',
                'm_tt.Tanggal',
                'm_tt.KodeCust',
                'm_cust.NamaCust',
                'm_tt.Keterangan'
            )
            ->get();

        return response()->json($vtt);
    }

    public function getVTTInvoice()
    {
        $vttInvoice = MTT::join('d_tt', 'm_tt.NoTT', '=', 'd_tt.NoTT')
            ->join('m_cust', 'm_tt.KodeCust', '=', 'm_cust.KodeCust')
            ->join('invoice', 'd_tt.NoRef', '=', 'invoice.NoInvoice')
            ->join('m_sales', 'invoice.KodeSales', '=', 'm_sales.KodeSales')
            ->select(
                'm_tt.NoTT',
                'm_tt.Tanggal',
                'm_tt.KodeCust',
                'm_cust.NamaCust',
                'm_tt.Keterangan',
                'd_tt.NoRef',
                'invoice.TglFaktur',
                'm_sales.NamaSales',
                'invoice.GrandTotal',
                'invoice.SisaInvoice'
            )
            ->get();

        return response()->json($vttInvoice);
    }

    public function getVTTRetur()
    {
        $vttRetur = MTT::join('m_cust', 'm_tt.KodeCust', '=', 'm_cust.KodeCust')
            ->join('d_tt', 'm_tt.NoTT', '=', 'd_tt.NoTT')
            ->join('return_sales', 'd_tt.NoRef', '=', 'return_sales.NoRetur')
            ->join('return_sales_detail', function ($join) {
                $join->on('return_sales.KodeDivisi', '=', 'return_sales_detail.KodeDivisi')
                     ->on('return_sales.NoRetur', '=', 'return_sales_detail.NoRetur');
            })
            ->join('m_barang', function ($join) {
                $join->on('return_sales_detail.KodeDivisi', '=', 'm_barang.KodeDivisi')
                     ->on('return_sales_detail.KodeBarang', '=', 'm_barang.KodeBarang');
            })
            ->select(
                'm_tt.NoTT',
                'm_tt.Tanggal',
                'm_tt.KodeCust',
                'm_cust.NamaCust',
                'd_tt.NoRef',
                'return_sales.TglRetur',
                'return_sales_detail.KodeBarang',
                'm_barang.NamaBarang',
                'return_sales_detail.QtyRetur',
                'return_sales_detail.HargaNett',
                'm_barang.merk',
                'return_sales.Status'
            )
            ->get();

        return response()->json($vttRetur);
    }
}
