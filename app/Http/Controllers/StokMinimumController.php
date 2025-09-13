<?php

namespace App\Http\Controllers;

use App\Models\StokMinimum;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class StokMinimumController extends Controller
{
    public function index(): JsonResponse
    {
        $stokMinimums = StokMinimum::with(['barang'])->get();
        return response()->json($stokMinimums);
    }

    public function create()
    {
        // Return view for create form if needed
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'kode_divisi' => 'required|string|max:5|exists:m_divisi,kode_divisi',
            'kode_barang' => 'required|string|max:15',
            'minimal' => 'required|integer|min:0'
        ]);

        $stokMinimum = StokMinimum::create($request->all());
        return response()->json($stokMinimum, 201);
    }

    public function show(string $id): JsonResponse
    {
        $stokMinimum = StokMinimum::with(['barang'])
            ->findOrFail($id);
        return response()->json($stokMinimum);
    }

    public function edit(string $id)
    {
        // Return view for edit form if needed
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'minimal' => 'required|integer|min:0'
        ]);

        $stokMinimum = StokMinimum::findOrFail($id);
        $stokMinimum->update($request->all());
        return response()->json($stokMinimum);
    }

    public function destroy(string $id): JsonResponse
    {
        $stokMinimum = StokMinimum::findOrFail($id);
        $stokMinimum->delete();
        return response()->json(['message' => 'StokMinimum deleted successfully']);
    }

    public function checkLowStock($kodeDivisi): JsonResponse
    {
        $lowStock = StokMinimum::select('stok_minimum.*')
            ->join('m_barang', 'm_barang.kode_barang', '=', 'stok_minimum.kode_barang')
            ->leftJoin('kartu_stok', function($join) {
                $join->on('kartu_stok.kode_barang', '=', 'stok_minimum.kode_barang')
                     ->on('kartu_stok.kode_divisi', '=', 'stok_minimum.kode_divisi');
            })
            ->where('stok_minimum.kode_divisi', $kodeDivisi)
            ->whereRaw('COALESCE(kartu_stok.stok_akhir, 0) <= stok_minimum.minimal')
            ->with(['barang'])
            ->get();
        
        return response()->json($lowStock);
    }

    public function getByDivisi($kodeDivisi): JsonResponse
    {
        $stokMinimums = StokMinimum::where('kode_divisi', $kodeDivisi)
            ->with(['barang'])
            ->get();
        return response()->json($stokMinimums);
    }
}
