<?php

namespace App\Http\Controllers;

use App\Models\DVoucher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DVoucherController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $dVouchers = DVoucher::with(['voucher'])->get();

            return response()->json([
                'success' => true,
                'data' => $dVouchers,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data detail voucher',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'no_voucher' => 'required|string|max:15|exists:m_voucher,no_voucher',
            'no_penerimaan' => 'required|string|max:15',
        ]);

        try {
            DB::beginTransaction();

            $dVoucher = DVoucher::create($request->all());
            $dVoucher->load('voucher');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Detail voucher berhasil dibuat',
                'data' => $dVoucher,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat detail voucher',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $dVoucher = DVoucher::with('voucher')->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $dVoucher,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data detail voucher',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'no_voucher' => 'required|string|max:15|exists:m_voucher,no_voucher',
            'no_penerimaan' => 'required|string|max:15',
        ]);

        try {
            DB::beginTransaction();

            $dVoucher = DVoucher::findOrFail($id);
            $dVoucher->update($request->all());
            $dVoucher->load('voucher');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Detail voucher berhasil diperbarui',
                'data' => $dVoucher,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui detail voucher',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            DB::beginTransaction();

            $dVoucher = DVoucher::findOrFail($id);
            $dVoucher->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Detail voucher berhasil dihapus',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus detail voucher',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
