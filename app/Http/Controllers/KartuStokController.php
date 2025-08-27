<?php

namespace App\Http\Controllers;

use App\Models\KartuStok;
use App\Models\MBarang;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class KartuStokController extends Controller
{
    /**
     * Display a listing of kartu stok (limited to 10 for testing)
     */
    public function index(): JsonResponse
    {
        try {
            $kartuStok = KartuStok::orderBy('tglproses', 'desc')
                ->orderBy('urut', 'desc')
                ->limit(10)
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Kartu stok data retrieved successfully',
                'data' => $kartuStok->map(fn($k)=>[
                    'id' => $k->id,
                    'kodeDivisi' => $k->kodedivisi,
                    'kodeBarang' => $k->kodebarang,
                    'tanggal' => $k->tanggal ?? $k->tglproses,
                    'stokMasuk' => $k->stok_masuk ?? $k->masuk,
                    'stokKeluar' => $k->stok_keluar ?? $k->keluar,
                    'stokAkhir' => $k->stok_akhir ?? $k->saldo,
                    'keterangan' => $k->keterangan
                ]),
                'totalCount' => $kartuStok->count(),
                'note' => 'Showing latest 10 records for testing purposes'
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error fetching kartu stok: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve kartu stok data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all kartu stok for frontend (unlimited)
     */
    public function getAllForFrontend(): JsonResponse
    {
        try {
            $kartuStok = KartuStok::orderBy('tglproses', 'desc')
                ->orderBy('urut', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'All kartu stok data retrieved successfully',
                'data' => $kartuStok->map(fn($k)=>[
                    'id' => $k->id,
                    'kodeDivisi' => $k->kodedivisi,
                    'kodeBarang' => $k->kodebarang,
                    'tanggal' => $k->tanggal ?? $k->tglproses,
                    'stokMasuk' => $k->stok_masuk ?? $k->masuk,
                    'stokKeluar' => $k->stok_keluar ?? $k->keluar,
                    'stokAkhir' => $k->stok_akhir ?? $k->saldo,
                    'keterangan' => $k->keterangan
                ]),
                'totalCount' => $kartuStok->count()
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error fetching all kartu stok: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve all kartu stok data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get kartu stok by barang code
     */
    public function getByBarang(string $kodeDivisi, string $kodeBarang): JsonResponse
    {
        try {
            $kartuStok = KartuStok::where('kodedivisi', $kodeDivisi)
                ->where('kodebarang', $kodeBarang)
                ->orderBy('tglproses', 'desc')
                ->orderBy('urut', 'desc')
                ->get();

            if ($kartuStok->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No kartu stok found for this barang',
                    'data' => []
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Kartu stok for barang retrieved successfully',
                'data' => $kartuStok->map(fn($k)=>[
                    'id' => $k->id,
                    'kodeDivisi' => $k->kodedivisi,
                    'kodeBarang' => $k->kodebarang,
                    'tanggal' => $k->tanggal ?? $k->tglproses,
                    'stokMasuk' => $k->stok_masuk ?? $k->masuk,
                    'stokKeluar' => $k->stok_keluar ?? $k->keluar,
                    'stokAkhir' => $k->stok_akhir ?? $k->saldo,
                ]),
                'kodeDivisi' => $kodeDivisi,
                'kodeBarang' => $kodeBarang,
                'totalCount' => $kartuStok->count()
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error fetching kartu stok by barang: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve kartu stok for this barang',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created kartu stok
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'kodedivisi' => 'required|string|max:10',
                'kodebarang' => 'required|string|max:20',
                'tanggal' => 'required|date',
                'noreferensi' => 'nullable|string|max:50',
                'jenistransaksi' => 'nullable|string|max:20',
                'masuk' => 'nullable|numeric',
                'keluar' => 'nullable|numeric',
                'saldo' => 'required|numeric',
                'harga' => 'nullable|numeric',
                'keterangan' => 'nullable|string'
            ]);

            // Verify barang exists
            $barang = MBarang::where('kodebarang', $validated['kodebarang'])->first();
            if (!$barang) {
                return response()->json([
                    'success' => false,
                    'message' => 'Barang not found with this code'
                ], 404);
            }

            $kartuStok = KartuStok::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Kartu stok created successfully',
                'data' => $kartuStok->load('barang:kodebarang,namabarang')
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('Error creating kartu stok: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to create kartu stok',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified kartu stok
     */
    public function show(int $id): JsonResponse
    {
        try {
            $kartuStok = KartuStok::with(['barang:kode_barang,nama_barang'])
                ->find($id);

            if (!$kartuStok) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kartu stok not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Kartu stok retrieved successfully',
                'data' => [
                    'id' => $kartuStok->id,
                    'kodeDivisi' => $kartuStok->kodedivisi,
                    'kodeBarang' => $kartuStok->kodebarang,
                    'tanggal' => $kartuStok->tanggal ?? $kartuStok->tglproses,
                    'stokMasuk' => $kartuStok->stok_masuk ?? $kartuStok->masuk,
                    'stokKeluar' => $kartuStok->stok_keluar ?? $kartuStok->keluar,
                    'stokAkhir' => $kartuStok->stok_akhir ?? $kartuStok->saldo,
                    'keterangan' => $kartuStok->keterangan
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error fetching kartu stok: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve kartu stok',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified kartu stok
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $kartuStok = KartuStok::find($id);

            if (!$kartuStok) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kartu stok not found'
                ], 404);
            }

            $validated = $request->validate([
                'tanggal' => 'sometimes|date',
                'jam' => 'sometimes|date_format:H:i:s',
                'kode_barang' => 'sometimes|string|max:20',
                'stok_awal' => 'sometimes|numeric',
                'stok_masuk' => 'nullable|numeric',
                'stok_keluar' => 'nullable|numeric',
                'stok_akhir' => 'sometimes|numeric',
                'keterangan' => 'nullable|string',
                'no_dokumen' => 'nullable|string|max:50',
                'tipe_transaksi' => 'nullable|string|max:20',
                'user_input' => 'nullable|string|max:50'
            ]);

            // Verify barang exists if kode_barang is being updated
            if (isset($validated['kode_barang'])) {
                $barang = MBarang::where('kode_barang', $validated['kode_barang'])->first();
                if (!$barang) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Barang not found with this code'
                    ], 404);
                }
            }

            $kartuStok->update($validated);

            $fresh = $kartuStok->fresh();
            return response()->json([
                'success' => true,
                'message' => 'Kartu stok updated successfully',
                'data' => [
                    'id' => $fresh->id,
                    'kodeDivisi' => $fresh->kodedivisi,
                    'kodeBarang' => $fresh->kodebarang,
                    'tanggal' => $fresh->tanggal ?? $fresh->tglproses,
                    'stokMasuk' => $fresh->stok_masuk ?? $fresh->masuk,
                    'stokKeluar' => $fresh->stok_keluar ?? $fresh->keluar,
                    'stokAkhir' => $fresh->stok_akhir ?? $fresh->saldo,
                    'keterangan' => $fresh->keterangan
                ]
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('Error updating kartu stok: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update kartu stok',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified kartu stok
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $kartuStok = KartuStok::find($id);

            if (!$kartuStok) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kartu stok not found'
                ], 404);
            }

            $kartuStok->delete();

            return response()->json([
                'success' => true,
                'message' => 'Kartu stok deleted successfully'
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error deleting kartu stok: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete kartu stok',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get stock summary by barang
     */
    public function getStockSummary(): JsonResponse
    {
        try {
            $summary = DB::table('dbo.kartustok as ks')
                ->join('dbo.mbarang as mb', 'ks.kode_barang', '=', 'mb.kode_barang')
                ->select([
                    'ks.kode_barang',
                    'mb.nama_barang',
                    DB::raw('SUM(ks.stok_masuk) as total_masuk'),
                    DB::raw('SUM(ks.stok_keluar) as total_keluar'),
                    DB::raw('MAX(ks.stok_akhir) as stok_terakhir'),
                    DB::raw('MAX(ks.tanggal) as tanggal_terakhir')
                ])
                ->groupBy('ks.kode_barang', 'mb.nama_barang')
                ->orderBy('mb.nama_barang')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Stock summary retrieved successfully',
                'data' => $summary->map(fn($s)=>[
                    'kodeBarang' => $s->kode_barang,
                    'namaBarang' => $s->nama_barang,
                    'totalMasuk' => $s->total_masuk,
                    'totalKeluar' => $s->total_keluar,
                    'stokTerakhir' => $s->stok_terakhir,
                    'tanggalTerakhir' => $s->tanggal_terakhir
                ]),
                'totalCount' => $summary->count()
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error getting stock summary: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve stock summary',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
