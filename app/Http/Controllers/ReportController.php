<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Get data from v_bank view
     */
    public function getBankReport(): JsonResponse
    {
        try {
            $data = DB::select('SELECT * FROM v_bank');
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get data from v_barang view
     */
    public function getBarangReport(Request $request): JsonResponse
    {
        try {
            $query = 'SELECT * FROM v_barang';
            $params = [];

            if ($request->has('kode_divisi')) {
                $query .= ' WHERE kode_divisi = ?';
                $params[] = $request->kode_divisi;
            }

            if ($request->has('kode_kategori')) {
                $query .= $request->has('kode_divisi') ? ' AND' : ' WHERE';
                $query .= ' kode_kategori = ?';
                $params[] = $request->kode_kategori;
            }

            $data = DB::select($query, $params);
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get data from v_invoice view
     */
    public function getInvoiceReport(Request $request): JsonResponse
    {
        try {
            $query = 'SELECT * FROM v_invoice';
            $params = [];

            if ($request->has('kode_divisi')) {
                $query .= ' WHERE kode_divisi = ?';
                $params[] = $request->kode_divisi;
            }

            if ($request->has('no_invoice')) {
                $query .= $request->has('kode_divisi') ? ' AND' : ' WHERE';
                $query .= ' no_invoice = ?';
                $params[] = $request->no_invoice;
            }

            if ($request->has('start_date') && $request->has('end_date')) {
                $query .= ($request->has('kode_divisi') || $request->has('no_invoice')) ? ' AND' : ' WHERE';
                $query .= ' tgl_faktur BETWEEN ? AND ?';
                $params[] = $request->start_date;
                $params[] = $request->end_date;
            }

            $data = DB::select($query, $params);
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get data from v_invoice_header view
     */
    public function getInvoiceHeaderReport(Request $request): JsonResponse
    {
        try {
            $query = 'SELECT * FROM v_invoice_header';
            $params = [];

            if ($request->has('kode_divisi')) {
                $query .= ' WHERE kode_divisi = ?';
                $params[] = $request->kode_divisi;
            }

            if ($request->has('status')) {
                $query .= $request->has('kode_divisi') ? ' AND' : ' WHERE';
                $query .= ' status = ?';
                $params[] = $request->status;
            }

            $data = DB::select($query, $params);
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get data from v_kartu_stok view
     */
    public function getKartuStokReport(Request $request): JsonResponse
    {
        try {
            $query = 'SELECT * FROM v_kartu_stok';
            $params = [];

            if ($request->has('kode_divisi')) {
                $query .= ' WHERE kode_divisi = ?';
                $params[] = $request->kode_divisi;
            }

            if ($request->has('kode_barang')) {
                $query .= $request->has('kode_divisi') ? ' AND' : ' WHERE';
                $query .= ' kode_barang = ?';
                $params[] = $request->kode_barang;
            }

            if ($request->has('start_date') && $request->has('end_date')) {
                $query .= ($request->has('kode_divisi') || $request->has('kode_barang')) ? ' AND' : ' WHERE';
                $query .= ' tgl_proses BETWEEN ? AND ?';
                $params[] = $request->start_date;
                $params[] = $request->end_date;
            }

            $query .= ' ORDER BY tgl_proses, urut';
            $data = DB::select($query, $params);
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get data from v_part_penerimaan view
     */
    public function getPartPenerimaanReport(Request $request): JsonResponse
    {
        try {
            $query = 'SELECT * FROM v_part_penerimaan';
            $params = [];

            if ($request->has('kode_divisi')) {
                $query .= ' WHERE kode_divisi = ?';
                $params[] = $request->kode_divisi;
            }

            if ($request->has('kode_supplier')) {
                $query .= $request->has('kode_divisi') ? ' AND' : ' WHERE';
                $query .= ' kode_supplier = ?';
                $params[] = $request->kode_supplier;
            }

            $data = DB::select($query, $params);
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get data from v_penerimaan_finance view
     */
    public function getPenerimaanFinanceReport(Request $request): JsonResponse
    {
        try {
            $query = 'SELECT * FROM v_penerimaan_finance';
            $params = [];

            if ($request->has('kode_divisi')) {
                $query .= ' WHERE kode_divisi = ?';
                $params[] = $request->kode_divisi;
            }

            if ($request->has('kode_cust')) {
                $query .= $request->has('kode_divisi') ? ' AND' : ' WHERE';
                $query .= ' kode_cust = ?';
                $params[] = $request->kode_cust;
            }

            $data = DB::select($query, $params);
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get data from v_return_sales_detail view
     */
    public function getReturnSalesDetailReport(Request $request): JsonResponse
    {
        try {
            $query = 'SELECT * FROM v_return_sales_detail';
            $params = [];

            if ($request->has('kode_divisi')) {
                $query .= ' WHERE kode_divisi = ?';
                $params[] = $request->kode_divisi;
            }

            if ($request->has('kode_cust')) {
                $query .= $request->has('kode_divisi') ? ' AND' : ' WHERE';
                $query .= ' kode_cust = ?';
                $params[] = $request->kode_cust;
            }

            $data = DB::select($query, $params);
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function stokSummary(Request $request): JsonResponse
    {
        try {
            $query = 'SELECT * FROM v_stok_summary';
            $params = [];

            if ($request->has('kode_divisi')) {
                $query .= ' WHERE kode_divisi = ?';
                $params[] = $request->kode_divisi;
            }

            if ($request->has('status_stok')) {
                $query .= $request->has('kode_divisi') ? ' AND' : ' WHERE';
                $query .= ' status_stok = ?';
                $params[] = $request->status_stok;
            }

            $data = DB::select($query, $params);
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function financialReport(Request $request): JsonResponse
    {
        try {
            $query = 'SELECT * FROM v_financial_report';
            $params = [];

            if ($request->has('start_date') && $request->has('end_date')) {
                $query .= ' WHERE tanggal BETWEEN ? AND ?';
                $params[] = $request->start_date;
                $params[] = $request->end_date;
            }

            if ($request->has('kode_coa')) {
                $query .= ($request->has('start_date') && $request->has('end_date')) ? ' AND' : ' WHERE';
                $query .= ' kode_coa = ?';
                $params[] = $request->kode_coa;
            }

            $query .= ' ORDER BY tanggal, kode_coa';
            $data = DB::select($query, $params);
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function agingReport(Request $request): JsonResponse
    {
        try {
            $query = 'SELECT * FROM v_aging_report';
            $params = [];

            if ($request->has('kode_divisi')) {
                $query .= ' WHERE kode_divisi = ?';
                $params[] = $request->kode_divisi;
            }

            if ($request->has('aging_category')) {
                $query .= $request->has('kode_divisi') ? ' AND' : ' WHERE';
                $query .= ' aging_category = ?';
                $params[] = $request->aging_category;
            }

            $query .= ' ORDER BY days_overdue DESC';
            $data = DB::select($query, $params);
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function salesSummary(Request $request): JsonResponse
    {
        try {
            $query = 'SELECT * FROM v_sales_summary';
            $params = [];

            if ($request->has('kode_divisi')) {
                $query .= ' WHERE kode_divisi = ?';
                $params[] = $request->kode_divisi;
            }

            if ($request->has('kode_sales')) {
                $query .= $request->has('kode_divisi') ? ' AND' : ' WHERE';
                $query .= ' kode_sales = ?';
                $params[] = $request->kode_sales;
            }

            if ($request->has('bulan') && $request->has('tahun')) {
                $query .= ($request->has('kode_divisi') || $request->has('kode_sales')) ? ' AND' : ' WHERE';
                $query .= ' bulan = ? AND tahun = ?';
                $params[] = $request->bulan;
                $params[] = $request->tahun;
            }

            $data = DB::select($query, $params);
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function returnSummary(Request $request): JsonResponse
    {
        try {
            $query = 'SELECT * FROM v_return_summary';
            $params = [];

            if ($request->has('kode_divisi')) {
                $query .= ' WHERE kode_divisi = ?';
                $params[] = $request->kode_divisi;
            }

            if ($request->has('kategori_retur')) {
                $query .= $request->has('kode_divisi') ? ' AND' : ' WHERE';
                $query .= ' kategori_retur = ?';
                $params[] = $request->kategori_retur;
            }

            $data = DB::select($query, $params);
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function dashboardKpi(): JsonResponse
    {
        try {
            $data = DB::select('SELECT * FROM v_dashboard_kpi');
            return response()->json($data[0] ?? []);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get data from v_journal view
     */
    public function getJournalReport(Request $request): JsonResponse
    {
        try {
            $query = 'SELECT * FROM v_journal';
            $params = [];

            if ($request->has('start_date') && $request->has('end_date')) {
                $query .= ' WHERE tanggal BETWEEN ? AND ?';
                $params[] = $request->start_date;
                $params[] = $request->end_date;
            }

            if ($request->has('kode_coa')) {
                $query .= ($request->has('start_date') && $request->has('end_date')) ? ' AND' : ' WHERE';
                $query .= ' kode_coa = ?';
                $params[] = $request->kode_coa;
            }

            $query .= ' ORDER BY tanggal, id';
            $data = DB::select($query, $params);
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get data from v_tt view (Tanda Terima)
     */
    public function getTtReport(Request $request): JsonResponse
    {
        try {
            $query = 'SELECT * FROM v_tt';
            $params = [];

            if ($request->has('kode_divisi')) {
                $query .= ' WHERE kode_divisi = ?';
                $params[] = $request->kode_divisi;
            }

            if ($request->has('kode_cust')) {
                $query .= $request->has('kode_divisi') ? ' AND' : ' WHERE';
                $query .= ' kode_cust = ?';
                $params[] = $request->kode_cust;
            }

            $data = DB::select($query, $params);
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get data from v_voucher view
     */
    public function getVoucherReport(Request $request): JsonResponse
    {
        try {
            $query = 'SELECT * FROM v_voucher';
            $params = [];

            if ($request->has('kode_divisi')) {
                $query .= ' WHERE kode_divisi = ?';
                $params[] = $request->kode_divisi;
            }

            if ($request->has('kode_sales')) {
                $query .= $request->has('kode_divisi') ? ' AND' : ' WHERE';
                $query .= ' kode_sales = ?';
                $params[] = $request->kode_sales;
            }

            $data = DB::select($query, $params);
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
