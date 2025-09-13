<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDBankRequest;
use App\Http\Requests\UpdateDBankRequest;
use App\Http\Resources\DBankCollection;
use App\Http\Resources\DBankResource;
use App\Models\DetailBank;
use App\Models\Divisi;
use App\Models\Bank;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DBankController extends Controller
{
    /**
     * Display a listing of bank accounts for a specific division.
     */
    public function index(Request $request, string $kodeDivisi): JsonResponse
    {
        // Verify division exists
        $divisi = Divisi::where('kode_divisi', $kodeDivisi)->first();
        if (!$divisi) {
            return response()->json([
                'success' => false,
                'message' => 'Divisi tidak ditemukan.',
            ], 404);
        }

        $query = DetailBank::where('kode_divisi', $kodeDivisi)
            ->with(['divisi', 'bank']);

        // Filter by bank if provided
        if ($request->filled('kode_bank')) {
            $query->where('kode_bank', $request->kode_bank);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by saldo conditions
        if ($request->filled('saldo_min')) {
            $query->where('saldo', '>=', $request->saldo_min);
        }

        if ($request->filled('saldo_max')) {
            $query->where('saldo', '<=', $request->saldo_max);
        }

        // Search in account name or number
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('no_rekening', 'like', "%{$search}%")
                  ->orWhere('atas_nama', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'no_rekening');
        $sortOrder = $request->get('sort_order', 'asc');
        
        $allowedSorts = ['no_rekening', 'atas_nama', 'saldo', 'status', 'created_at'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->orderBy('no_rekening', 'asc');
        }

        $bankAccounts = $query->get();

        return response()->json([
            'success' => true,
            'message' => 'Data rekening bank berhasil diambil.',
            'data' => new DBankCollection($bankAccounts),
            'division_info' => [
                'kode_divisi' => $divisi->kode_divisi,
                'nama_divisi' => $divisi->nama_divisi ?? null,
            ]
        ]);
    }

    /**
     * Store a newly created bank account.
     */
    public function store(StoreDBankRequest $request, string $kodeDivisi): JsonResponse
    {
        // Verify division exists
        $divisi = Divisi::where('kode_divisi', $kodeDivisi)->first();
        if (!$divisi) {
            return response()->json([
                'success' => false,
                'message' => 'Divisi tidak ditemukan.',
            ], 404);
        }

        // Verify bank exists if provided
        if ($request->filled('kode_bank')) {
            $bank = Bank::where('kode_bank', $request->kode_bank)->first();
            if (!$bank) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bank tidak ditemukan.',
                ], 404);
            }
        }

        $bankAccount = DetailBank::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Rekening bank berhasil dibuat.',
            'data' => new DBankResource($bankAccount->load(['divisi', 'bank']))
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $kodeDivisi, string $noRekening): JsonResponse
    {
        $bankAccount = DetailBank::where('kode_divisi', $kodeDivisi)
            ->where('no_rekening', $noRekening)
            ->with(['divisi', 'bank', 'saldoBanks'])
            ->first();

        if (!$bankAccount) {
            return response()->json([
                'success' => false,
                'message' => 'Rekening bank tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail rekening bank berhasil diambil.',
            'data' => new DBankResource($bankAccount)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDBankRequest $request, string $kodeDivisi, string $noRekening): JsonResponse
    {
        $bankAccount = DetailBank::where('kode_divisi', $kodeDivisi)
            ->where('no_rekening', $noRekening)
            ->first();

        if (!$bankAccount) {
            return response()->json([
                'success' => false,
                'message' => 'Rekening bank tidak ditemukan.',
            ], 404);
        }

        // Verify bank exists if being updated
        if ($request->filled('kode_bank')) {
            $bank = Bank::where('kode_bank', $request->kode_bank)->first();
            if (!$bank) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bank tidak ditemukan.',
                ], 404);
            }
        }

        $bankAccount->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Rekening bank berhasil diperbarui.',
            'data' => new DBankResource($bankAccount->load(['divisi', 'bank']))
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $kodeDivisi, string $noRekening): JsonResponse
    {
        $bankAccount = DetailBank::where('kode_divisi', $kodeDivisi)
            ->where('no_rekening', $noRekening)
            ->first();

        if (!$bankAccount) {
            return response()->json([
                'success' => false,
                'message' => 'Rekening bank tidak ditemukan.',
            ], 404);
        }

        // Check if account has related transactions
        if ($bankAccount->saldoBanks()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Rekening bank tidak dapat dihapus karena memiliki riwayat transaksi.',
            ], 422);
        }

        $bankAccount->delete();

        return response()->json([
            'success' => true,
            'message' => 'Rekening bank berhasil dihapus.',
        ]);
    }

    /**
     * Get bank account statistics for a division.
     */
    public function statistics(string $kodeDivisi): JsonResponse
    {
        // Verify division exists
        $divisi = Divisi::where('kode_divisi', $kodeDivisi)->first();
        if (!$divisi) {
            return response()->json([
                'success' => false,
                'message' => 'Divisi tidak ditemukan.',
            ], 404);
        }

        $accounts = DetailBank::where('kode_divisi', $kodeDivisi)->get();

        $stats = [
            'total_accounts' => $accounts->count(),
            'total_saldo' => $accounts->sum('saldo'),
            'average_saldo' => $accounts->count() > 0 ? $accounts->avg('saldo') : 0,
            'max_saldo' => $accounts->max('saldo') ?? 0,
            'min_saldo' => $accounts->min('saldo') ?? 0,
            'accounts_by_status' => $accounts->groupBy('status')->map->count(),
            'accounts_by_bank' => $accounts->groupBy('kode_bank')->map->count(),
            'saldo_distribution' => [
                'positive' => $accounts->where('saldo', '>', 0)->count(),
                'zero' => $accounts->where('saldo', '=', 0)->count(),
                'negative' => $accounts->where('saldo', '<', 0)->count(),
            ],
            'formatted_totals' => [
                'total_saldo' => 'Rp ' . number_format($accounts->sum('saldo'), 2, ',', '.'),
                'average_saldo' => 'Rp ' . number_format($accounts->count() > 0 ? $accounts->avg('saldo') : 0, 2, ',', '.'),
            ]
        ];

        return response()->json([
            'success' => true,
            'message' => 'Statistik rekening bank berhasil diambil.',
            'data' => $stats,
            'division_info' => [
                'kode_divisi' => $divisi->kode_divisi,
                'nama_divisi' => $divisi->nama_divisi ?? null,
            ]
        ]);
    }
}
