<?php

namespace App\Http\Controllers;

use App\Models\MDokumen;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MDokumenController extends Controller
{
    public function index(): JsonResponse
    {
        $mDokumens = MDokumen::with(['divisi', 'transactionType'])->get();
        return response()->json($mDokumens);
    }

    public function create()
    {
        // Return view for create form if needed
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'kode_divisi' => 'required|string|max:5|exists:m_divisi,kode_divisi',
            'kode_trans' => 'required|string|max:10|exists:m_trans,kode_trans',
            'nomor' => 'required|integer|min:1',
            'prefix' => 'nullable|string|max:10',
            'tahun' => 'required|integer|min:2000|max:2100',
            'bulan' => 'required|integer|min:1|max:12'
        ]);

        $mDokumen = MDokumen::create($request->all());
        return response()->json($mDokumen, 201);
    }

    public function show(string $kodeDivisi, string $kodeTrans): JsonResponse
    {
        $mDokumen = MDokumen::with(['divisi', 'transactionType'])
            ->where('kode_divisi', $kodeDivisi)
            ->where('kode_trans', $kodeTrans)
            ->firstOrFail();
        return response()->json($mDokumen);
    }

    public function edit(string $kodeDivisi, string $kodeTrans)
    {
        // Return view for edit form if needed
    }

    public function update(Request $request, string $kodeDivisi, string $kodeTrans): JsonResponse
    {
        $request->validate([
            'nomor' => 'required|integer|min:1',
            'prefix' => 'nullable|string|max:10',
            'tahun' => 'required|integer|min:2000|max:2100',
            'bulan' => 'required|integer|min:1|max:12'
        ]);

        $mDokumen = MDokumen::where('kode_divisi', $kodeDivisi)
            ->where('kode_trans', $kodeTrans)
            ->firstOrFail();
        
        $mDokumen->update($request->all());
        return response()->json($mDokumen);
    }

    public function destroy(string $kodeDivisi, string $kodeTrans): JsonResponse
    {
        $mDokumen = MDokumen::where('kode_divisi', $kodeDivisi)
            ->where('kode_trans', $kodeTrans)
            ->firstOrFail();
        
        $mDokumen->delete();
        return response()->json(['message' => 'MDokumen deleted successfully']);
    }
}
