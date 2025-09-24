<?php

namespace App\Http\Controllers;

use App\Models\SaldoBank;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
            'no_rekening' => 'required|string|max:50|exists:bank,no_rekening',
            'tgl_proses' => 'required|date',
            'keterangan' => 'nullable|string|max:500',
            'debet' => 'nullable|numeric|min:0',
            'kredit' => 'nullable|numeric|min:0',
            'saldo' => 'nullable|numeric',
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
            'no_rekening' => 'required|string|max:50|exists:bank,no_rekening',
            'tgl_proses' => 'required|date',
            'keterangan' => 'nullable|string|max:500',
            'debet' => 'nullable|numeric|min:0',
            'kredit' => 'nullable|numeric|min:0',
            'saldo' => 'nullable|numeric',
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

    public function getByBank($noRekening): JsonResponse
    {
        $saldoBanks = SaldoBank::where('no_rekening', $noRekening)
            ->orderBy('tgl_proses', 'desc')
            ->get();

        return response()->json($saldoBanks);
    }

    public function getLatest($noRekening): JsonResponse
    {
        $latestSaldo = SaldoBank::where('no_rekening', $noRekening)
            ->orderBy('tgl_proses', 'desc')
            ->first();

        return response()->json($latestSaldo);
    }
}
