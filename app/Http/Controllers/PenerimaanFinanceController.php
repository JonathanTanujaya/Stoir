<?php

namespace App\Http\Controllers;

use App\Models\PenerimaanFinance;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PenerimaanFinanceController extends Controller
{
    public function index(): JsonResponse
    {
        $penerimaanFinances = PenerimaanFinance::with(['divisi', 'bank', 'detailBank', 'penerimaanFinanceDetails'])->get();
        return response()->json($penerimaanFinances);
    }

    public function create()
    {
        // Return view for create form if needed
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'kode_divisi' => 'required|string|max:5|exists:m_divisi,kode_divisi',
            'no_penerimaan_finance' => 'required|string|max:20',
            'tgl_penerimaan' => 'required|date',
            'kode_bank' => 'required|string|max:10',
            'no_rekening' => 'required|string|max:20',
            'nilai' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string|max:255'
        ]);

        $penerimaanFinance = PenerimaanFinance::create($request->all());
        return response()->json($penerimaanFinance, 201);
    }

    public function show(string $kodeDivisi, string $noPenerimaanFinance): JsonResponse
    {
        $penerimaanFinance = PenerimaanFinance::with(['divisi', 'bank', 'detailBank', 'penerimaanFinanceDetails'])
            ->where('kode_divisi', $kodeDivisi)
            ->where('no_penerimaan_finance', $noPenerimaanFinance)
            ->firstOrFail();
        return response()->json($penerimaanFinance);
    }

    public function edit(string $kodeDivisi, string $noPenerimaanFinance)
    {
        // Return view for edit form if needed
    }

    public function update(Request $request, string $kodeDivisi, string $noPenerimaanFinance): JsonResponse
    {
        $request->validate([
            'tgl_penerimaan' => 'required|date',
            'kode_bank' => 'required|string|max:10',
            'no_rekening' => 'required|string|max:20',
            'nilai' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string|max:255'
        ]);

        $penerimaanFinance = PenerimaanFinance::where('kode_divisi', $kodeDivisi)
            ->where('no_penerimaan_finance', $noPenerimaanFinance)
            ->firstOrFail();
        
        $penerimaanFinance->update($request->all());
        return response()->json($penerimaanFinance);
    }

    public function destroy(string $kodeDivisi, string $noPenerimaanFinance): JsonResponse
    {
        $penerimaanFinance = PenerimaanFinance::where('kode_divisi', $kodeDivisi)
            ->where('no_penerimaan_finance', $noPenerimaanFinance)
            ->firstOrFail();
        
        $penerimaanFinance->delete();
        return response()->json(['message' => 'PenerimaanFinance deleted successfully']);
    }
}
