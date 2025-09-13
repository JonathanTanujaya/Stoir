<?php

namespace App\Http\Controllers;

use App\Models\DPaket;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DPaketController extends Controller
{
    public function index(): JsonResponse
    {
        $dPakets = DPaket::with(['divisi', 'barang'])->get();
        return response()->json($dPakets);
    }

    public function create()
    {
        // Return view for create form if needed
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'kode_divisi' => 'required|string|max:5|exists:m_divisi,kode_divisi',
            'kode_paket' => 'required|string|max:15',
            'kode_barang' => 'required|string|max:15|exists:m_barang,kode_barang',
            'qty' => 'required|integer|min:1'
        ]);

        $dPaket = DPaket::create($request->all());
        return response()->json($dPaket, 201);
    }

    public function show(string $kodeDivisi, string $kodePaket, string $kodeBarang): JsonResponse
    {
        $dPaket = DPaket::with(['divisi', 'barang'])
            ->where('kode_divisi', $kodeDivisi)
            ->where('kode_paket', $kodePaket)
            ->where('kode_barang', $kodeBarang)
            ->firstOrFail();
        return response()->json($dPaket);
    }

    public function edit(string $kodeDivisi, string $kodePaket, string $kodeBarang)
    {
        // Return view for edit form if needed
    }

    public function update(Request $request, string $kodeDivisi, string $kodePaket, string $kodeBarang): JsonResponse
    {
        $request->validate([
            'qty' => 'required|integer|min:1'
        ]);

        $dPaket = DPaket::where('kode_divisi', $kodeDivisi)
            ->where('kode_paket', $kodePaket)
            ->where('kode_barang', $kodeBarang)
            ->firstOrFail();
        
        $dPaket->update($request->all());
        return response()->json($dPaket);
    }

    public function destroy(string $kodeDivisi, string $kodePaket, string $kodeBarang): JsonResponse
    {
        $dPaket = DPaket::where('kode_divisi', $kodeDivisi)
            ->where('kode_paket', $kodePaket)
            ->where('kode_barang', $kodeBarang)
            ->firstOrFail();
        
        $dPaket->delete();
        return response()->json(['message' => 'DPaket deleted successfully']);
    }
}
