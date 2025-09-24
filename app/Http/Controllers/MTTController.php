<?php

namespace App\Http\Controllers;

use App\Models\MTt;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MTTController extends Controller
{
    /**
     * Display a listing of tanda terima with relationships.
     */
    public function index(): JsonResponse
    {
        try {
            $mTts = MTt::with(['customer', 'dTts'])->get();

            return response()->json([
                'success' => true,
                'data' => $mTts,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data tanda terima',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created tanda terima.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'no_tt' => 'required|string|max:15|unique:m_tt,no_tt',
            'tanggal' => 'required|date',
            'kode_cust' => 'required|string|max:5|exists:m_cust,kode_cust',
            'keterangan' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $mTt = MTt::create($request->all());
            $mTt->load(['customer']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Tanda terima berhasil dibuat',
                'data' => $mTt,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat tanda terima',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified tanda terima.
     */
    public function show(string $noTt): JsonResponse
    {
        try {
            $mTt = MTt::with(['customer', 'dTts'])
                ->where('no_tt', $noTt)
                ->firstOrFail();

            return response()->json([
                'success' => true,
                'data' => $mTt,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data tanda terima',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified tanda terima.
     */
    public function update(Request $request, string $noTt): JsonResponse
    {
        $request->validate([
            'tanggal' => 'required|date',
            'kode_cust' => 'required|string|max:5|exists:m_cust,kode_cust',
            'keterangan' => 'nullable|string|max:500',
        ]);

        try {
            $mTt = MTt::where('no_tt', $noTt)->firstOrFail();

            DB::beginTransaction();

            $mTt->update($request->all());
            $mTt->load(['customer']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Tanda terima berhasil diperbarui',
                'data' => $mTt,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui tanda terima',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified tanda terima.
     */
    public function destroy(string $noTt): JsonResponse
    {
        try {
            $mTt = MTt::where('no_tt', $noTt)->firstOrFail();

            DB::beginTransaction();

            // Check if has related details
            $hasDetails = $mTt->dTts()->exists();
            if ($hasDetails) {
                DB::rollBack();

                return response()->json([
                    'success' => false,
                    'message' => 'Tanda terima tidak dapat dihapus karena memiliki detail terkait',
                ], 422);
            }

            $mTt->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Tanda terima berhasil dihapus',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus tanda terima',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
