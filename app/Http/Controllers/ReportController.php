<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
            $conditions = [];

            if ($request->has('kode_kategori')) {
                $conditions[] = 'kode_kategori = ?';
                $params[] = $request->kode_kategori;
            }

            if (! empty($conditions)) {
                $query .= ' WHERE '.implode(' AND ', $conditions);
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
            $conditions = [];

            if ($request->has('no_invoice')) {
                $conditions[] = 'no_invoice = ?';
                $params[] = $request->no_invoice;
            }

            if ($request->has('start_date') && $request->has('end_date')) {
                $conditions[] = 'tgl_faktur BETWEEN ? AND ?';
                $params[] = $request->start_date;
                $params[] = $request->end_date;
            }

            if (! empty($conditions)) {
                $query .= ' WHERE '.implode(' AND ', $conditions);
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
            $conditions = [];

            if ($request->has('status')) {
                $conditions[] = 'status = ?';
                $params[] = $request->status;
            }

            if (! empty($conditions)) {
                $query .= ' WHERE '.implode(' AND ', $conditions);
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
            $conditions = [];

            if ($request->has('kode_barang')) {
                $conditions[] = 'kode_barang = ?';
                $params[] = $request->kode_barang;
            }

            if ($request->has('start_date') && $request->has('end_date')) {
                $conditions[] = 'tgl_proses BETWEEN ? AND ?';
                $params[] = $request->start_date;
                $params[] = $request->end_date;
            }

            if (! empty($conditions)) {
                $query .= ' WHERE '.implode(' AND ', $conditions);
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
            $conditions = [];

            if ($request->has('kode_supplier')) {
                $conditions[] = 'kode_supplier = ?';
                $params[] = $request->kode_supplier;
            }

            if (! empty($conditions)) {
                $query .= ' WHERE '.implode(' AND ', $conditions);
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
            $conditions = [];

            if ($request->has('kode_cust')) {
                $conditions[] = 'kode_cust = ?';
                $params[] = $request->kode_cust;
            }

            if (! empty($conditions)) {
                $query .= ' WHERE '.implode(' AND ', $conditions);
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
            $conditions = [];

            if ($request->has('kode_cust')) {
                $conditions[] = 'kode_cust = ?';
                $params[] = $request->kode_cust;
            }

            if (! empty($conditions)) {
                $query .= ' WHERE '.implode(' AND ', $conditions);
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
            $conditions = [];

            if ($request->has('status_stok')) {
                $conditions[] = 'status_stok = ?';
                $params[] = $request->status_stok;
            }

            if (! empty($conditions)) {
                $query .= ' WHERE '.implode(' AND ', $conditions);
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
            $conditions = [];

            if ($request->has('start_date') && $request->has('end_date')) {
                $conditions[] = 'tanggal BETWEEN ? AND ?';
                $params[] = $request->start_date;
                $params[] = $request->end_date;
            }

            if ($request->has('kode_coa')) {
                $conditions[] = 'kode_coa = ?';
                $params[] = $request->kode_coa;
            }

            if (! empty($conditions)) {
                $query .= ' WHERE '.implode(' AND ', $conditions);
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
            $conditions = [];

            if ($request->has('aging_category')) {
                $conditions[] = 'aging_category = ?';
                $params[] = $request->aging_category;
            }

            if (! empty($conditions)) {
                $query .= ' WHERE '.implode(' AND ', $conditions);
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
            $conditions = [];

            if ($request->has('kode_sales')) {
                $conditions[] = 'kode_sales = ?';
                $params[] = $request->kode_sales;
            }

            if ($request->has('bulan') && $request->has('tahun')) {
                $conditions[] = 'bulan = ? AND tahun = ?';
                $params[] = $request->bulan;
                $params[] = $request->tahun;
            }

            if (! empty($conditions)) {
                $query .= ' WHERE '.implode(' AND ', $conditions);
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
            $conditions = [];

            if ($request->has('kategori_retur')) {
                $conditions[] = 'kategori_retur = ?';
                $params[] = $request->kategori_retur;
            }

            if (! empty($conditions)) {
                $query .= ' WHERE '.implode(' AND ', $conditions);
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
            $conditions = [];

            if ($request->has('start_date') && $request->has('end_date')) {
                $conditions[] = 'tanggal BETWEEN ? AND ?';
                $params[] = $request->start_date;
                $params[] = $request->end_date;
            }

            if ($request->has('kode_coa')) {
                $conditions[] = 'kode_coa = ?';
                $params[] = $request->kode_coa;
            }

            if (! empty($conditions)) {
                $query .= ' WHERE '.implode(' AND ', $conditions);
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
            $conditions = [];

            if ($request->has('kode_cust')) {
                $conditions[] = 'kode_cust = ?';
                $params[] = $request->kode_cust;
            }

            if (! empty($conditions)) {
                $query .= ' WHERE '.implode(' AND ', $conditions);
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
            $conditions = [];

            if ($request->has('kode_sales')) {
                $conditions[] = 'kode_sales = ?';
                $params[] = $request->kode_sales;
            }

            if (! empty($conditions)) {
                $query .= ' WHERE '.implode(' AND ', $conditions);
            }

            $data = DB::select($query, $params);

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
