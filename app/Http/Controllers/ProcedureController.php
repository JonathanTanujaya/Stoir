<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ProcedureController extends Controller
{
    public function createInvoice(Request $request): JsonResponse
    {
        $request->validate([
            'kode_divisi' => 'required|string|max:5',
            'no_invoice' => 'required|string|max:15',
            'kode_cust' => 'required|string|max:5',
            'kode_sales' => 'required|string|max:5',
            'tipe' => 'required|string|size:1',
            'total' => 'required|numeric',
            'disc' => 'required|numeric',
            'pajak' => 'required|numeric',
            'grand_total' => 'required|numeric',
            'ket' => 'nullable|string',
            'kode_barang' => 'required|string|max:50',
            'qty_supply' => 'required|integer',
            'harga_jual' => 'required|numeric',
            'diskon1' => 'required|numeric',
            'diskon2' => 'required|numeric',
            'harga_nett' => 'required|numeric',
            'user' => 'required|string|max:50'
        ]);

        try {
            DB::select('CALL sp_invoice(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [
                $request->kode_divisi,
                $request->no_invoice,
                $request->kode_cust,
                $request->kode_sales,
                $request->tipe,
                $request->total,
                $request->disc,
                $request->pajak,
                $request->grand_total,
                $request->ket,
                $request->kode_barang,
                $request->qty_supply,
                $request->harga_jual,
                $request->diskon1,
                $request->diskon2,
                $request->harga_nett,
                $request->user
            ]);

            return response()->json(['message' => 'Invoice created successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function createPartPenerimaan(Request $request): JsonResponse
    {
        $request->validate([
            'kode_divisi' => 'required|string|max:4',
            'no_penerimaan' => 'required|string|max:15',
            'tgl_penerimaan' => 'required|date',
            'kode_valas' => 'required|string|max:3',
            'kurs' => 'required|numeric',
            'kode_supplier' => 'required|string|max:5',
            'jatuh_tempo' => 'required|date',
            'no_faktur' => 'required|string|max:50',
            'total' => 'required|numeric',
            'disc' => 'required|numeric',
            'pajak' => 'required|numeric',
            'grand_total' => 'required|numeric',
            'kode_barang' => 'required|string|max:30',
            'qty_supply' => 'required|integer',
            'harga' => 'required|numeric',
            'harga_nett' => 'required|numeric',
            'diskon1' => 'required|numeric',
            'diskon2' => 'required|numeric'
        ]);

        try {
            DB::select('CALL sp_part_penerimaan(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [
                $request->kode_divisi,
                $request->no_penerimaan,
                $request->tgl_penerimaan,
                $request->kode_valas,
                $request->kurs,
                $request->kode_supplier,
                $request->jatuh_tempo,
                $request->no_faktur,
                $request->total,
                $request->disc,
                $request->pajak,
                $request->grand_total,
                $request->kode_barang,
                $request->qty_supply,
                $request->harga,
                $request->harga_nett,
                $request->diskon1,
                $request->diskon2
            ]);

            return response()->json(['message' => 'Part penerimaan created successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function createReturSales(Request $request): JsonResponse
    {
        $request->validate([
            'kode_divisi' => 'required|string|max:4',
            'no_retur' => 'required|string|max:15',
            'kode_cust' => 'required|string|max:5',
            'ket' => 'nullable|string',
            'total' => 'required|numeric',
            'no_invoice' => 'required|string|max:15',
            'kode_barang' => 'required|string|max:30',
            'qty_retur' => 'required|integer',
            'harga_nett' => 'required|numeric'
        ]);

        try {
            DB::select('CALL sp_retur_sales(?, ?, ?, ?, ?, ?, ?, ?, ?)', [
                $request->kode_divisi,
                $request->no_retur,
                $request->kode_cust,
                $request->ket,
                $request->total,
                $request->no_invoice,
                $request->kode_barang,
                $request->qty_retur,
                $request->harga_nett
            ]);

            return response()->json(['message' => 'Retur sales created successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function batalkanInvoice(Request $request): JsonResponse
    {
        $request->validate([
            'kode_divisi' => 'required|string|max:5',
            'no_invoice' => 'required|string|max:15'
        ]);

        try {
            DB::select('CALL sp_pembatalan_invoice(?, ?)', [
                $request->kode_divisi,
                $request->no_invoice
            ]);

            return response()->json(['message' => 'Invoice cancelled successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function stokOpname(Request $request): JsonResponse
    {
        $request->validate([
            'kode_divisi' => 'required|string|max:4',
            'no_opname' => 'required|string|max:15',
            'total' => 'required|numeric',
            'kode_barang' => 'required|string|max:30',
            'qty' => 'required|integer',
            'modal' => 'required|numeric',
            'id' => 'required|integer'
        ]);

        try {
            DB::select('CALL sp_opname(?, ?, ?, ?, ?, ?, ?)', [
                $request->kode_divisi,
                $request->no_opname,
                $request->total,
                $request->kode_barang,
                $request->qty,
                $request->modal,
                $request->id
            ]);

            return response()->json(['message' => 'Stock opname completed successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function generateNomor(Request $request): JsonResponse
    {
        $request->validate([
            'kode_divisi' => 'required|string|max:4',
            'kode_dok' => 'required|string|max:50'
        ]);

        try {
            $result = DB::select('CALL sp_set_nomor(?, ?, @nomor)', [
                $request->kode_divisi,
                $request->kode_dok
            ]);

            $nomor = DB::select('SELECT @nomor as nomor')[0]->nomor;

            return response()->json(['nomor' => $nomor]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Create master resi (sp_master_resi)
     */
    public function createMasterResi(Request $request): JsonResponse
    {
        $request->validate([
            'kode_divisi' => 'required|string|max:2',
            'no_resi' => 'required|string|max:20',
            'tgl_resi' => 'required|date',
            'kode_cust' => 'required|string|max:10',
            'alamat' => 'required|string|max:200',
            'kota' => 'required|string|max:50',
            'petugas' => 'required|string|max:50',
            'ongkir' => 'required|numeric|min:0',
            'total_barang' => 'required|integer|min:0',
            'keterangan' => 'nullable|string|max:200',
        ]);

        try {
            $result = DB::select('CALL sp_master_resi(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [
                $request->kode_divisi,
                $request->no_resi,
                $request->tgl_resi,
                $request->kode_cust,
                $request->alamat,
                $request->kota,
                $request->petugas,
                $request->ongkir,
                $request->total_barang,
                $request->keterangan,
            ]);

            return response()->json(['success' => true, 'data' => $result]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Tambah saldo bank (sp_tambah_saldo)
     */
    public function tambahSaldo(Request $request): JsonResponse
    {
        $request->validate([
            'kode_divisi' => 'required|string|max:2',
            'kode_bank' => 'required|string|max:5',
            'tanggal' => 'required|date',
            'jumlah' => 'required|numeric',
            'keterangan' => 'nullable|string|max:200',
            'ref_no' => 'nullable|string|max:20',
            'jenis' => 'required|in:D,K',
        ]);

        try {
            $result = DB::select('CALL sp_tambah_saldo(?, ?, ?, ?, ?, ?, ?)', [
                $request->kode_divisi,
                $request->kode_bank,
                $request->tanggal,
                $request->jumlah,
                $request->keterangan,
                $request->ref_no,
                $request->jenis,
            ]);

            return response()->json(['success' => true, 'data' => $result]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Create tanda terima (sp_tanda_terima)
     */
    public function createTandaTerima(Request $request): JsonResponse
    {
        $request->validate([
            'kode_divisi' => 'required|string|max:2',
            'no_tt' => 'required|string|max:20',
            'tgl_tt' => 'required|date',
            'kode_cust' => 'required|string|max:10',
            'kode_sales' => 'required|string|max:10',
            'total_bruto' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'total_netto' => 'required|numeric|min:0',
            'ppn' => 'nullable|numeric|min:0',
            'total_invoice' => 'required|numeric|min:0',
            'status' => 'required|in:Y,N',
            'keterangan' => 'nullable|string|max:200',
        ]);

        try {
            $result = DB::select('CALL sp_tanda_terima(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [
                $request->kode_divisi,
                $request->no_tt,
                $request->tgl_tt,
                $request->kode_cust,
                $request->kode_sales,
                $request->total_bruto,
                $request->discount,
                $request->total_netto,
                $request->ppn,
                $request->total_invoice,
                $request->status,
                $request->keterangan,
            ]);

            return response()->json(['success' => true, 'data' => $result]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Create voucher (sp_voucher)
     */
    public function createVoucher(Request $request): JsonResponse
    {
        $request->validate([
            'kode_divisi' => 'required|string|max:2',
            'no_voucher' => 'required|string|max:20',
            'tgl_voucher' => 'required|date',
            'kode_sales' => 'required|string|max:10',
            'kode_cust' => 'nullable|string|max:10',
            'jenis' => 'required|in:M,T',
            'nominal' => 'required|numeric|min:0',
            'expired_date' => 'nullable|date',
            'status' => 'required|in:A,U',
            'keterangan' => 'nullable|string|max:200',
        ]);

        try {
            $result = DB::select('CALL sp_voucher(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [
                $request->kode_divisi,
                $request->no_voucher,
                $request->tgl_voucher,
                $request->kode_sales,
                $request->kode_cust,
                $request->jenis,
                $request->nominal,
                $request->expired_date,
                $request->status,
                $request->keterangan,
            ]);

            return response()->json(['success' => true, 'data' => $result]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Merge barang (sp_merge_barang)
     */
    public function mergeBarang(Request $request): JsonResponse
    {
        $request->validate([
            'kode_divisi' => 'required|string|max:2',
            'kode_barang_dari' => 'required|string|max:20',
            'kode_barang_ke' => 'required|string|max:20',
            'user_id' => 'required|string|max:20',
        ]);

        try {
            $result = DB::select('CALL sp_merge_barang(?, ?, ?, ?)', [
                $request->kode_divisi,
                $request->kode_barang_dari,
                $request->kode_barang_ke,
                $request->user_id,
            ]);

            return response()->json(['success' => true, 'data' => $result]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Journal invoice (sp_journal_invoice)
     */
    public function journalInvoice(Request $request): JsonResponse
    {
        $request->validate([
            'kode_divisi' => 'required|string|max:2',
            'no_invoice' => 'required|string|max:20',
            'tanggal' => 'required|date',
            'user_id' => 'required|string|max:20',
        ]);

        try {
            $result = DB::select('CALL sp_journal_invoice(?, ?, ?, ?)', [
                $request->kode_divisi,
                $request->no_invoice,
                $request->tanggal,
                $request->user_id,
            ]);

            return response()->json(['success' => true, 'data' => $result]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Journal retur sales (sp_journal_retur_sales)
     */
    public function journalReturSales(Request $request): JsonResponse
    {
        $request->validate([
            'kode_divisi' => 'required|string|max:2',
            'no_retur' => 'required|string|max:20',
            'tanggal' => 'required|date',
            'user_id' => 'required|string|max:20',
        ]);

        try {
            $result = DB::select('CALL sp_journal_retur_sales(?, ?, ?, ?)', [
                $request->kode_divisi,
                $request->no_retur,
                $request->tanggal,
                $request->user_id,
            ]);

            return response()->json(['success' => true, 'data' => $result]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Journal penerimaan (sp_journal_penerimaan)
     */
    public function journalPenerimaan(Request $request): JsonResponse
    {
        $request->validate([
            'kode_divisi' => 'required|string|max:2',
            'no_penerimaan' => 'required|string|max:20',
            'tanggal' => 'required|date',
            'user_id' => 'required|string|max:20',
        ]);

        try {
            $result = DB::select('CALL sp_journal_penerimaan(?, ?, ?, ?)', [
                $request->kode_divisi,
                $request->no_penerimaan,
                $request->tanggal,
                $request->user_id,
            ]);

            return response()->json(['success' => true, 'data' => $result]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
