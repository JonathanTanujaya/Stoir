<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class BankController extends Controller
{
    public function index(): JsonResponse
    {
        $banks = Bank::with(['saldoBanks', 'penerimaanFinances'])->get();
        return response()->json($banks);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'no_rekening' => 'required|string|max:50|unique:bank,no_rekening',
            'atas_nama' => 'required|string|max:50',
            'kode_bank' => 'nullable|string|max:5',
            'nama_bank' => 'required|string|max:50',
            'saldo' => 'nullable|numeric|min:0',
            'status_rekening' => 'nullable|string|max:50',
            'status_bank' => 'nullable|boolean'
        ]);

        $bank = Bank::create($request->all());
        return response()->json($bank, 201);
    }

    public function show(string $noRekening): JsonResponse
    {
        $bank = Bank::with(['saldoBanks', 'penerimaanFinances'])
            ->findOrFail($noRekening);
        return response()->json($bank);
    }

    public function update(Request $request, string $noRekening): JsonResponse
    {
        $request->validate([
            'atas_nama' => 'sometimes|string|max:50',
            'kode_bank' => 'sometimes|nullable|string|max:5',
            'nama_bank' => 'sometimes|string|max:50',
            'saldo' => 'sometimes|nullable|numeric|min:0',
            'status_rekening' => 'sometimes|nullable|string|max:50',
            'status_bank' => 'sometimes|nullable|boolean'
        ]);

        $bank = Bank::findOrFail($noRekening);
        $bank->update($request->all());
        return response()->json($bank);
    }

    public function destroy(string $noRekening): JsonResponse
    {
        $bank = Bank::findOrFail($noRekening);
        
        // Check if bank has related records
        if ($bank->saldoBanks()->exists() || $bank->penerimaanFinances()->exists()) {
            return response()->json([
                'message' => 'Cannot delete bank account with existing transactions'
            ], 422);
        }
        
        $bank->delete();
        return response()->json(['message' => 'Bank deleted successfully']);
    }
}
