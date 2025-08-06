<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\InvoiceBonusService;
use App\Models\InvoiceBonus;
use App\Models\MCust;
use App\Models\InvoiceBonusDetail;
use App\Models\MBarang;

class InvoiceBonusController extends Controller
{
    protected $invoiceBonusService;

    public function __construct(InvoiceBonusService $invoiceBonusService)
    {
        $this->invoiceBonusService = $invoiceBonusService;
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
        try {
            $this->invoiceBonusService->createInvoiceBonus(
                $request->input('KodeDivisi'),
                $request->input('NoInvoice'),
                $request->input('KodeCust'),
                $request->input('KodeBarang'),
                $request->input('QtySupply'),
                $request->input('username')
            );

            return response()->json(['message' => 'Invoice Bonus created successfully'], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function getVInvoiceBonusHeader()
    {
        $vInvoiceBonusHeader = InvoiceBonus::join('m_cust', function ($join) {
            $join->on('invoice_bonus.KodeDivisi', '=', 'm_cust.KodeDivisi')
                 ->on('invoice_bonus.KodeCust', '=', 'm_cust.KodeCust');
        })
        ->select(
            'invoice_bonus.KodeDivisi',
            'invoice_bonus.NoInvoiceBonus',
            'invoice_bonus.TglFaktur',
            'invoice_bonus.KodeCust',
            'm_cust.NamaCust',
            'invoice_bonus.Ket',
            'invoice_bonus.Status',
            'invoice_bonus.username'
        )
        ->get();

        return response()->json($vInvoiceBonusHeader);
    }

    public function getVInvoiceBonus()
    {
        $vInvoiceBonus = MBarang::rightJoin('invoice_bonus_detail', function ($join) {
            $join->on('m_barang.KodeDivisi', '=', 'invoice_bonus_detail.KodeDivisi')
                 ->on('m_barang.KodeBarang', '=', 'invoice_bonus_detail.KodeBarang');
        })
        ->leftJoin('invoice_bonus', function ($join) {
            $join->on('invoice_bonus_detail.KodeDivisi', '=', 'invoice_bonus.KodeDivisi')
                 ->on('invoice_bonus_detail.NoInvoiceBonus', '=', 'invoice_bonus.NoInvoiceBonus');
        })
        ->leftJoin('m_cust', function ($join) {
            $join->on('invoice_bonus.KodeCust', '=', 'm_cust.KodeCust')
                 ->on('invoice_bonus.KodeDivisi', '=', 'm_cust.KodeDivisi');
        })
        ->select(
            'invoice_bonus.KodeDivisi',
            'invoice_bonus.NoInvoiceBonus',
            'invoice_bonus.TglFaktur',
            'invoice_bonus.KodeCust',
            'm_cust.NamaCust',
            'invoice_bonus.Ket',
            'invoice_bonus.Status',
            'invoice_bonus.username',
            'invoice_bonus_detail.KodeBarang',
            'm_barang.NamaBarang',
            'invoice_bonus_detail.QtySupply',
            'invoice_bonus_detail.HargaNett',
            DB::raw('m_cust.Alamat AS AlamatCust'),
            'm_barang.Satuan',
            'm_barang.merk'
        )
        ->get();

        return response()->json($vInvoiceBonus);
    }
}
