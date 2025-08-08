<?php

namespace App\Http\Controllers;

use App\Models\MBank;
use App\Models\DBank;
use Illuminate\Http\Request;

class MBankController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $banks = MBank::active()->get();
            
            return response()->json([
                'success' => true,
                'message' => 'Data banks retrieved successfully',
                'data' => $banks
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve banks data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'kodedivisi' => 'required|string|max:2',
                'kodebank' => 'required|string|max:10',
                'norekening' => 'required|string|max:50',
                'atasnama' => 'required|string|max:100',
                'saldo' => 'nullable|numeric',
                'status' => 'boolean'
            ]);

            $bank = MBank::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Bank created successfully',
                'data' => $bank
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create bank',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display banks by division.
     */
    public function showByDivisi($kodeDivisi)
    {
        try {
            $banks = MBank::byDivisi($kodeDivisi)->active()->get();
            
            return response()->json([
                'success' => true,
                'message' => 'Banks retrieved successfully',
                'data' => $banks
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve banks',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($kodeDivisi, $kodeBank)
    {
        try {
            $bank = MBank::findByCompositeKey($kodeDivisi, $kodeBank);
            
            if (!$bank) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bank not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Bank retrieved successfully',
                'data' => $bank
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve bank',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $kodeDivisi, $kodeBank)
    {
        try {
            $bank = MBank::findByCompositeKey($kodeDivisi, $kodeBank);
            
            if (!$bank) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bank not found'
                ], 404);
            }

            $request->validate([
                'norekening' => 'required|string|max:50',
                'atasnama' => 'required|string|max:100',
                'saldo' => 'nullable|numeric',
                'status' => 'boolean'
            ]);

            $bank->update($request->only(['norekening', 'atasnama', 'saldo', 'status']));

            return response()->json([
                'success' => true,
                'message' => 'Bank updated successfully',
                'data' => $bank
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update bank',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($kodeDivisi, $kodeBank)
    {
        try {
            $bank = MBank::findByCompositeKey($kodeDivisi, $kodeBank);
            
            if (!$bank) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bank not found'
                ], 404);
            }

            $bank->delete();

            return response()->json([
                'success' => true,
                'message' => 'Bank deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete bank',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getVBank()
    {
        $vBank = MBank::rightJoin('d_bank', function ($join) {
            $join->on('m_bank.KodeDivisi', '=', 'd_bank.KodeDivisi')
                 ->on('m_bank.KodeBank', '=', 'd_bank.KodeBank');
        })
        ->select(
            'd_bank.KodeDivisi',
            'd_bank.NoRekening',
            'd_bank.KodeBank',
            'm_bank.NamaBank',
            'd_bank.AtasNama',
            'd_bank.Status'
        )
        ->get();

        return response()->json($vBank);
    }
}
