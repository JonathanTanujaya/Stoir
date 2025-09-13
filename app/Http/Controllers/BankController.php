<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class BankController extends Controller
{
    public function index(): JsonResponse
    {
        $banks = Bank::with(['divisi', 'detailBanks'])->get();
        return response()->json($banks);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'kode_divisi' => 'required|string|max:5|exists:m_divisi,kode_divisi',
            'kode_bank' => 'required|string|max:10'
        ]);

        $bank = Bank::create($request->all());
        return response()->json($bank, 201);
    }

    public function show(string $kodeDivisi, string $kodeBank): JsonResponse
    {
        $bank = Bank::with(['divisi', 'detailBanks'])
            ->where('kode_divisi', $kodeDivisi)
            ->where('kode_bank', $kodeBank)
            ->firstOrFail();
        return response()->json($bank);
    }

    public function update(Request $request, string $kodeDivisi, string $kodeBank): JsonResponse
    {
        $bank = Bank::where('kode_divisi', $kodeDivisi)
            ->where('kode_bank', $kodeBank)
            ->firstOrFail();
        $bank->update($request->all());
        return response()->json($bank);
    }

    public function destroy(string $kodeDivisi, string $kodeBank): JsonResponse
    {
        $bank = Bank::where('kode_divisi', $kodeDivisi)
            ->where('kode_bank', $kodeBank)
            ->firstOrFail();
        $bank->delete();
        return response()->json(['message' => 'Bank deleted successfully']);
    }
}
