<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\InvoiceService;
use App\Models\Invoice;
use App\Models\MCust;
use App\Models\MSales;
use App\Models\MArea;
use App\Models\MBarang;
use App\Models\MKategori;
use App\Models\Company;
use App\Models\InvoiceDetail;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    protected $invoiceService;

    public function __construct(InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
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
            $this->invoiceService->createInvoice(
                $request->input('KodeDivisi'),
                $request->input('NoInvoice'),
                $request->input('KodeCust'),
                $request->input('KodeSales'),
                $request->input('Tipe'),
                $request->input('Total'),
                $request->input('Disc'),
                $request->input('Pajak'),
                $request->input('GrandTotal'),
                $request->input('Ket'),
                $request->input('KodeBarang'),
                $request->input('QtySupply'),
                $request->input('HargaJual'),
                $request->input('Diskon1'),
                $request->input('Diskon2'),
                $request->input('HargaNett'),
                $request->input('username')
            );

            return response()->json(['message' => 'Invoice created successfully'], 201);
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

    public function getVInvoice()
    {
        $vInvoice = Invoice::rightJoin('invoice_detail', function ($join) {
            $join->on('invoice.KodeDivisi', '=', 'invoice_detail.KodeDivisi')
                 ->on('invoice.NoInvoice', '=', 'invoice_detail.NoInvoice');
        })
        ->leftJoin('m_barang', function ($join) {
            $join->on('m_barang.KodeDivisi', '=', 'invoice_detail.KodeDivisi')
                 ->on('m_barang.KodeBarang', '=', 'invoice_detail.KodeBarang');
        })
        ->leftJoin('m_kategori', function ($join) {
            $join->on('m_barang.KodeDivisi', '=', 'm_kategori.KodeDivisi')
                 ->on('m_barang.KodeKategori', '=', 'm_kategori.KodeKategori');
        })
        ->leftJoin('m_cust', function ($join) {
            $join->on('invoice.KodeDivisi', '=', 'm_cust.KodeDivisi')
                 ->on('invoice.KodeCust', '=', 'm_cust.KodeCust');
        })
        ->leftJoin('m_area', function ($join) {
            $join->on('m_cust.KodeDivisi', '=', 'm_area.KodeDivisi')
                 ->on('m_cust.KodeArea', '=', 'm_area.KodeArea');
        })
        ->leftJoin('m_sales', function ($join) {
            $join->on('invoice.KodeDivisi', '=', 'm_sales.KodeDivisi')
                 ->on('invoice.KodeSales', '=', 'm_sales.KodeSales');
        })
        ->crossJoin('company')
        ->select(
            'invoice.NoInvoice',
            'invoice.TglFaktur',
            'invoice.KodeCust',
            'm_cust.NamaCust',
            'invoice.KodeSales',
            'm_sales.NamaSales',
            'm_cust.KodeDivisi',
            'm_cust.KodeArea',
            'm_area.Area',
            'invoice.Tipe',
            'invoice.JatuhTempo',
            'invoice.GrandTotal',
            'invoice.SisaInvoice',
            'invoice.Ket',
            'invoice.Status',
            'invoice_detail.KodeBarang',
            'm_barang.NamaBarang',
            'm_barang.KodeKategori',
            'm_kategori.Kategori',
            'invoice_detail.QtySupply',
            'invoice_detail.HargaJual',
            'invoice_detail.Jenis',
            'invoice_detail.Diskon1',
            'invoice_detail.Diskon2',
            'invoice_detail.HargaNett',
            DB::raw('invoice_detail.Status AS StatusDetail'),
            'invoice_detail.ID',
            'm_barang.merk',
            DB::raw('m_cust.Alamat AS AlamatCust'),
            'invoice.Total',
            'invoice.Disc',
            'invoice.Pajak',
            'company.CompanyName',
            'company.Alamat',
            'company.Kota',
            'company.AN',
            DB::raw('m_cust.Telp AS TelpCust'),
            DB::raw('m_cust.NoNPWP AS NPWPCust'),
            DB::raw('company.Telp'),
            DB::raw('company.NPWP'),
            'm_barang.Satuan',
            'invoice.username',
            'invoice.TT'
        )
        ->get();

        return response()->json($vInvoice);
    }

    public function getVInvoiceHeader()
    {
        $vInvoiceHeader = MArea::rightJoin('m_cust', function ($join) {
            $join->on('m_area.KodeArea', '=', 'm_cust.KodeArea')
                 ->on('m_area.KodeDivisi', '=', 'm_cust.KodeDivisi');
        })
        ->rightJoin('invoice', function ($join) {
            $join->on('m_cust.KodeDivisi', '=', 'invoice.KodeDivisi')
                 ->on('m_cust.KodeCust', '=', 'invoice.KodeCust');
        })
        ->leftJoin('m_sales', function ($join) {
            $join->on('invoice.KodeDivisi', '=', 'm_sales.KodeDivisi')
                 ->on('invoice.KodeSales', '=', 'm_sales.KodeSales');
        })
        ->select(
            'invoice.NoInvoice',
            'invoice.TglFaktur',
            'invoice.KodeCust',
            'm_cust.NamaCust',
            'm_cust.KodeArea',
            'm_area.Area',
            'invoice.KodeSales',
            'm_sales.NamaSales',
            'invoice.Tipe',
            'invoice.JatuhTempo',
            'invoice.GrandTotal',
            'invoice.SisaInvoice',
            'invoice.Ket',
            'invoice.Status',
            'invoice.KodeDivisi',
            'invoice.Total',
            'invoice.Disc',
            'invoice.Pajak',
            'm_cust.NoNPWP',
            'm_cust.NamaPajak',
            'm_cust.AlamatPajak',
            'invoice.username',
            'invoice.TT'
        )
        ->get();

        return response()->json($vInvoiceHeader);
    }
}
