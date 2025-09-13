<?php

namespace App\Http\Controllers;

use App\Models\SaldoBank;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SaldoBankController extends Controller
{
    public function index(): JsonResponse
    {
        $saldoBanks = SaldoBank::with(['bank'])->get();
        return response()->json($saldoBanks);
    }

    public function create()
    {
        // Return view for create form if needed
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'kode_divisi' => 'required|string|max:5|exists:m_divisi,kode_divisi',
            'kode_bank' => 'required|string|max:10',
            'tanggal' => 'required|date',
            'saldo' => 'required|numeric'
        ]);

        $saldoBank = SaldoBank::create($request->all());
        return response()->json($saldoBank, 201);
    }

    public function show(string $id): JsonResponse
    {
        $saldoBank = SaldoBank::with(['bank'])
            ->findOrFail($id);
        return response()->json($saldoBank);
    }

    public function edit(string $id)
    {
        // Return view for edit form if needed
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'tanggal' => 'required|date',
            'saldo' => 'required|numeric'
        ]);

        $saldoBank = SaldoBank::findOrFail($id);
        $saldoBank->update($request->all());
        return response()->json($saldoBank);
    }

    public function destroy(string $id): JsonResponse
    {
        $saldoBank = SaldoBank::findOrFail($id);
        $saldoBank->delete();
        return response()->json(['message' => 'SaldoBank deleted successfully']);
    }

    public function getByBank($kodeDivisi, $kodeBank): JsonResponse
    {
        $saldoBanks = SaldoBank::where('kode_divisi', $kodeDivisi)
            ->where('kode_bank', $kodeBank)
            ->orderBy('tanggal', 'desc')
            ->get();
        return response()->json($saldoBanks);
    }

    public function getLatest($kodeDivisi, $kodeBank): JsonResponse
    {
        $latestSaldo = SaldoBank::where('kode_divisi', $kodeDivisi)
            ->where('kode_bank', $kodeBank)
            ->orderBy('tanggal', 'desc')
            ->first();
        return response()->json($latestSaldo);
    }
}
