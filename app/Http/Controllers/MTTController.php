<?php

namespace App\Http\Controllers;

use App\Models\MTt;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MTtController extends Controller
{
    public function index(): JsonResponse
    {
        $mTts = MTt::with(['divisi', 'customer', 'dTts'])->get();
        return response()->json($mTts);
    }

    public function create()
    {
        // Return view for create form if needed
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'kode_divisi' => 'required|string|max:5|exists:m_divisi,kode_divisi',
            'no_tt' => 'required|string|max:20',
            'tgl_tt' => 'required|date',
            'kode_cust' => 'required|string|max:15',
            'nilai' => 'required|numeric|min:0',
            'status' => 'required|boolean'
        ]);

        $mTt = MTt::create($request->all());
        return response()->json($mTt, 201);
    }

    public function show(string $kodeDivisi, string $noTt): JsonResponse
    {
        $mTt = MTt::with(['divisi', 'customer', 'dTts'])
            ->where('kode_divisi', $kodeDivisi)
            ->where('no_tt', $noTt)
            ->firstOrFail();
        return response()->json($mTt);
    }

    public function edit(string $kodeDivisi, string $noTt)
    {
        // Return view for edit form if needed
    }

    public function update(Request $request, string $kodeDivisi, string $noTt): JsonResponse
    {
        $request->validate([
            'tgl_tt' => 'required|date',
            'kode_cust' => 'required|string|max:15',
            'nilai' => 'required|numeric|min:0',
            'status' => 'required|boolean'
        ]);

        $mTt = MTt::where('kode_divisi', $kodeDivisi)
            ->where('no_tt', $noTt)
            ->firstOrFail();
        
        $mTt->update($request->all());
        return response()->json($mTt);
    }

    public function destroy(string $kodeDivisi, string $noTt): JsonResponse
    {
        $mTt = MTt::where('kode_divisi', $kodeDivisi)
            ->where('no_tt', $noTt)
            ->firstOrFail();
        
        $mTt->delete();
        return response()->json(['message' => 'MTt deleted successfully']);
    }
}
