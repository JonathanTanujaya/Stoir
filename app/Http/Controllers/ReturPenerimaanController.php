<?php

namespace App\Http\Controllers;

use App\Models\ReturPenerimaan;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ReturPenerimaanController extends Controller
{
    public function index(): JsonResponse
    {
        $returPenerimaans = ReturPenerimaan::with(['divisi', 'supplier', 'partPenerimaan', 'returPenerimaanDetails'])->get();
        return response()->json($returPenerimaans);
    }

    public function create()
    {
        // Return view for create form if needed
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'kode_divisi' => 'required|string|max:5|exists:m_divisi,kode_divisi',
            'no_retur_penerimaan' => 'required|string|max:20',
            'tgl_retur' => 'required|date',
            'kode_supplier' => 'required|string|max:15',
            'no_penerimaan' => 'required|string|max:20',
            'nilai' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string|max:255'
        ]);

        $returPenerimaan = ReturPenerimaan::create($request->all());
        return response()->json($returPenerimaan, 201);
    }

    public function show(string $kodeDivisi, string $noReturPenerimaan): JsonResponse
    {
        $returPenerimaan = ReturPenerimaan::with(['divisi', 'supplier', 'partPenerimaan', 'returPenerimaanDetails'])
            ->where('kode_divisi', $kodeDivisi)
            ->where('no_retur_penerimaan', $noReturPenerimaan)
            ->firstOrFail();
        return response()->json($returPenerimaan);
    }

    public function edit(string $kodeDivisi, string $noReturPenerimaan)
    {
        // Return view for edit form if needed
    }

    public function update(Request $request, string $kodeDivisi, string $noReturPenerimaan): JsonResponse
    {
        $request->validate([
            'tgl_retur' => 'required|date',
            'kode_supplier' => 'required|string|max:15',
            'no_penerimaan' => 'required|string|max:20',
            'nilai' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string|max:255'
        ]);

        $returPenerimaan = ReturPenerimaan::where('kode_divisi', $kodeDivisi)
            ->where('no_retur_penerimaan', $noReturPenerimaan)
            ->firstOrFail();
        
        $returPenerimaan->update($request->all());
        return response()->json($returPenerimaan);
    }

    public function destroy(string $kodeDivisi, string $noReturPenerimaan): JsonResponse
    {
        $returPenerimaan = ReturPenerimaan::where('kode_divisi', $kodeDivisi)
            ->where('no_retur_penerimaan', $noReturPenerimaan)
            ->firstOrFail();
        
        $returPenerimaan->delete();
        return response()->json(['message' => 'ReturPenerimaan deleted successfully']);
    }
}
