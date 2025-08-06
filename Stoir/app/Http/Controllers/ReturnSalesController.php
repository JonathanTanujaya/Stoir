<?php

namespace App\Http\Controllers;

use App\Models\ReturnSales;
use App\Models\MCust;
use App\Models\Invoice;
use App\Models\MSales;
use App\Models\ReturnSalesDetail;
use App\Models\MBarang;
use Illuminate\Http\Request;

class ReturnSalesController extends Controller
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
    public function show(ReturnSales $returnSales)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ReturnSales $returnSales)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ReturnSales $returnSales)
    {
        //
    }

    public function getVCustRetur()
    {
        $vCustRetur = ReturnSales::leftJoin('m_cust', function ($join) {
            $join->on('return_sales.KodeDivisi', '=', 'm_cust.KodeDivisi')
                 ->on('return_sales.KodeCust', '=', 'm_cust.KodeCust');
        })
        ->select(
            'return_sales.KodeDivisi',
            'return_sales.NoRetur',
            'return_sales.TglRetur',
            'return_sales.KodeCust',
            'm_cust.NamaCust',
            'return_sales.Total',
            'return_sales.SisaRetur',
            'return_sales.Keterangan',
            'return_sales.Status'
        )
        ->get();

        return response()->json($vCustRetur);
    }

    public function getVReturnSalesDetail()
    {
        $vReturnSalesDetail = MSales::rightJoin('invoice', function ($join) {
            $join->on('m_sales.KodeDivisi', '=', 'invoice.KodeDivisi')
                 ->on('m_sales.KodeSales', '=', 'invoice.KodeSales');
        })
        ->rightJoin('return_sales', function ($join) {
            $join->on('return_sales.KodeDivisi', '=', 'invoice.KodeDivisi')
                 ->on('return_sales.NoRetur', '=', 'invoice.NoInvoice');
        })
        ->leftJoin('m_cust', function ($join) {
            $join->on('return_sales.KodeDivisi', '=', 'm_cust.KodeDivisi')
                 ->on('return_sales.KodeCust', '=', 'm_cust.KodeCust');
        })
        ->rightJoin('return_sales_detail', function ($join) {
            $join->on('return_sales.KodeDivisi', '=', 'return_sales_detail.KodeDivisi')
                 ->on('return_sales.NoRetur', '=', 'return_sales_detail.NoRetur');
        })
        ->leftJoin('m_barang', function ($join) {
            $join->on('return_sales_detail.KodeDivisi', '=', 'm_barang.KodeDivisi')
                 ->on('return_sales_detail.KodeBarang', '=', 'm_barang.KodeBarang');
        })
        ->select(
            'return_sales.KodeDivisi',
            'return_sales.NoRetur',
            'return_sales.TglRetur',
            'return_sales.KodeCust',
            'm_cust.NamaCust',
            'm_cust.Alamat AS AlamatCust',
            'return_sales.Total',
            'return_sales_detail.NoInvoice',
            'invoice.TglFaktur',
            'invoice.KodeSales',
            'm_sales.NamaSales',
            'return_sales_detail.KodeBarang',
            'm_barang.NamaBarang',
            'm_barang.Satuan',
            'm_barang.merk',
            'return_sales_detail.QtyRetur',
            'return_sales_detail.HargaNett',
            'm_cust.Telp',
            'return_sales.Status',
            'return_sales.TT',
            'return_sales.SisaRetur'
        )
        ->get();

        return response()->json($vReturnSalesDetail);
    }
}
