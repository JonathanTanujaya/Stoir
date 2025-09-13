<?php

namespace App\Http\Controllers;

use App\Models\DVoucher;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DVoucherController extends Controller
{
    public function index(): JsonResponse
    {
        $dVouchers = DVoucher::with(['divisi', 'sales', 'customer', 'invoice'])->get();
        return response()->json($dVouchers);
    }

    public function create()
    {
        // Return view for create form if needed
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'kode_divisi' => 'required|string|max:5|exists:m_divisi,kode_divisi',
            'no_voucher' => 'required|string|max:20',
            'kode_sales' => 'required|string|max:10',
            'no_invoice' => 'required|string|max:20',
            'tgl_invoice' => 'required|date',
            'kode_cust' => 'required|string|max:15',
            'nilai' => 'required|numeric|min:0'
        ]);

        $dVoucher = DVoucher::create($request->all());
        return response()->json($dVoucher, 201);
    }

    public function show(string $kodeDivisi, string $noVoucher, string $noInvoice): JsonResponse
    {
        $dVoucher = DVoucher::with(['divisi', 'sales', 'customer', 'invoice'])
            ->where('kode_divisi', $kodeDivisi)
            ->where('no_voucher', $noVoucher)
            ->where('no_invoice', $noInvoice)
            ->firstOrFail();
        return response()->json($dVoucher);
    }

    public function edit(string $kodeDivisi, string $noVoucher, string $noInvoice)
    {
        // Return view for edit form if needed
    }

    public function update(Request $request, string $kodeDivisi, string $noVoucher, string $noInvoice): JsonResponse
    {
        $request->validate([
            'kode_sales' => 'required|string|max:10',
            'tgl_invoice' => 'required|date',
            'kode_cust' => 'required|string|max:15',
            'nilai' => 'required|numeric|min:0'
        ]);

        $dVoucher = DVoucher::where('kode_divisi', $kodeDivisi)
            ->where('no_voucher', $noVoucher)
            ->where('no_invoice', $noInvoice)
            ->firstOrFail();
        
        $dVoucher->update($request->all());
        return response()->json($dVoucher);
    }

    public function destroy(string $kodeDivisi, string $noVoucher, string $noInvoice): JsonResponse
    {
        $dVoucher = DVoucher::where('kode_divisi', $kodeDivisi)
            ->where('no_voucher', $noVoucher)
            ->where('no_invoice', $noInvoice)
            ->firstOrFail();
        
        $dVoucher->delete();
        return response()->json(['message' => 'DVoucher deleted successfully']);
    }
}
