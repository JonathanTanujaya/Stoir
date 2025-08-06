<?php

namespace App\Http\Controllers;

use App\Models\PenerimaanFinance;
use App\Models\MCust;
use App\Models\PenerimaanFinanceDetail;
use App\Models\Invoice;
use App\Models\MSales;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PenerimaanFinanceController extends Controller
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
    public function show(PenerimaanFinance $penerimaanFinance)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PenerimaanFinance $penerimaanFinance)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PenerimaanFinance $penerimaanFinance)
    {
        //
    }

    public function getVPenerimaanFinance()
    {
        $vPenerimaanFinance = PenerimaanFinance::leftJoin('m_cust', function ($join) {
            $join->on('penerimaan_finance.KodeDivisi', '=', 'm_cust.KodeDivisi')
                 ->on('penerimaan_finance.KodeCust', '=', 'm_cust.KodeCust');
        })
        ->select(
            'penerimaan_finance.KodeDivisi',
            'penerimaan_finance.NoPenerimaan',
            'penerimaan_finance.TglPenerimaan',
            'penerimaan_finance.Tipe',
            'penerimaan_finance.NoRef',
            'penerimaan_finance.TglRef',
            'penerimaan_finance.TglPencairan',
            'penerimaan_finance.BankRef',
            'penerimaan_finance.NoRekTujuan',
            'penerimaan_finance.KodeCust',
            'penerimaan_finance.Jumlah',
            'penerimaan_finance.Status',
            'm_cust.NamaCust'
        )
        ->get();

        return response()->json($vPenerimaanFinance);
    }

    public function getVPenerimaanFinanceDetail()
    {
        $vPenerimaanFinanceDetail = PenerimaanFinanceDetail::leftJoin('invoice', function ($join) {
            $join->on('penerimaan_finance_detail.KodeDivisi', '=', 'invoice.KodeDivisi')
                 ->on('penerimaan_finance_detail.Noinvoice', '=', 'invoice.NoInvoice');
        })
        ->leftJoin('m_sales', function ($join) {
            $join->on('m_sales.KodeSales', '=', 'invoice.KodeSales')
                 ->on('m_sales.KodeDivisi', '=', 'invoice.KodeDivisi');
        })
        ->leftJoin('m_cust', function ($join) {
            $join->on('m_cust.KodeDivisi', '=', 'invoice.KodeDivisi')
                 ->on('m_cust.KodeCust', '=', 'invoice.KodeCust');
        })
        ->leftJoin('penerimaan_finance', function ($join) {
            $join->on('penerimaan_finance_detail.KodeDivisi', '=', 'penerimaan_finance.KodeDivisi')
                 ->on('penerimaan_finance_detail.NoPenerimaan', '=', 'penerimaan_finance.NoPenerimaan');
        })
        ->select(
            'penerimaan_finance.KodeDivisi',
            'penerimaan_finance.NoPenerimaan',
            'penerimaan_finance.TglPenerimaan',
            'penerimaan_finance.Tipe',
            'penerimaan_finance.NoRef',
            'penerimaan_finance.TglRef',
            'penerimaan_finance.TglPencairan',
            'penerimaan_finance.BankRef',
            'penerimaan_finance.NoRekTujuan',
            'penerimaan_finance.KodeCust',
            'penerimaan_finance.Jumlah',
            'penerimaan_finance.Status',
            'penerimaan_finance_detail.Noinvoice',
            'penerimaan_finance_detail.JumlahInvoice',
            'penerimaan_finance_detail.JumlahBayar',
            'penerimaan_finance_detail.JumlahDispensasi',
            DB::raw('penerimaan_finance_detail.Status AS StatusDetail'),
            'penerimaan_finance_detail.id',
            'invoice.SisaInvoice',
            DB::raw('penerimaan_finance_detail.SisaInvoice - penerimaan_finance_detail.JumlahBayar - penerimaan_finance_detail.JumlahDispensasi AS SisaBayar'),
            'm_cust.NamaCust',
            'invoice.KodeSales',
            'm_sales.NamaSales',
            'penerimaan_finance.NoVoucher'
        )
        ->get();

        return response()->json($vPenerimaanFinanceDetail);
    }
}
