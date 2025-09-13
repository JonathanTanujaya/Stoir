<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreKategoriRequest;
use App\Http\Requests\UpdateKategoriRequest;
use App\Http\Resources\KategoriCollection;
use App\Http\Resources\KategoriResource;
use App\Models\Kategori;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, string $kodeDivisi): JsonResponse
    {
        try {
            $request->attributes->set('query_start_time', microtime(true));

            $query = Kategori::query()
                ->where('kode_divisi', $kodeDivisi)
                ->with(['divisi', 'barangs', 'dPakets']);

            // Search functionality (PostgreSQL friendly)
            if ($request->filled('search')) {
                $search = $request->get('search');
                $query->where(function ($q) use ($search) {
                    $q->where('kode_kategori', 'ILIKE', "%{$search}%")
                      ->orWhere('kategori', 'ILIKE', "%{$search}%");
                });
            }

            // Filter by status
            if ($request->filled('status')) {
                $query->where('status', $request->boolean('status'));
            }

            // Sorting
            $sortBy = $request->get('sort', $request->get('sort_by', 'kode_kategori'));
            $sortOrder = $request->get('direction', $request->get('sort_order', 'asc'));
            $allowedSort = ['kode_kategori', 'kategori', 'status'];
            if (in_array($sortBy, $allowedSort, true)) {
                $query->orderBy($sortBy, $sortOrder === 'desc' ? 'desc' : 'asc');
            }

            // Pagination
            $perPage = min((int) $request->get('per_page', 15), 100);
            $kategoris = $query->paginate($perPage);

            return response()->json(new KategoriCollection($kategoris));
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data kategori',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreKategoriRequest $request, string $kodeDivisi): JsonResponse
    {
        try {
            $data = $request->validated();
            $data['kode_divisi'] = $kodeDivisi;
            $data['status'] = $data['status'] ?? true;

            $kategori = Kategori::create($data);
            $kategori->load(['divisi']);
            
            return response()->json([
                'success' => true,
                'message' => 'Kategori berhasil dibuat',
                'data' => (new KategoriResource($kategori))->resolve($request),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat kategori',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $kodeDivisi, string $kodeKategori): JsonResponse
    {
        try {
            $kategori = Kategori::with(['divisi', 'barangs', 'dPakets'])
                ->where('kode_divisi', $kodeDivisi)
                ->where('kode_kategori', $kodeKategori)
                ->firstOrFail();
            
            return response()->json([
                'success' => true,
                'data' => (new KategoriResource($kategori))->resolve(request()),
            ]);
        } catch (\Exception $e) {
            $status = $e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException ? 404 : 500;
            return response()->json([
                'success' => false,
                'message' => $status === 404 ? 'Kategori tidak ditemukan' : 'Gagal mengambil data kategori',
                'error' => $e->getMessage(),
            ], $status);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateKategoriRequest $request, string $kodeDivisi, string $kodeKategori): JsonResponse
    {
        try {
            $kategori = Kategori::where('kode_divisi', $kodeDivisi)
                ->where('kode_kategori', $kodeKategori)
                ->firstOrFail();

            $data = $request->validated();
            
            if (isset($data['kode_kategori']) && $data['kode_kategori'] !== $kodeKategori) {
                $newData = array_merge($kategori->toArray(), $data);
                $newData['kode_divisi'] = $kodeDivisi;
                $newKategori = Kategori::create($newData);
                $kategori->delete();
                $newKategori->load(['divisi']);
                $kategori = $newKategori;
            } else {
                \DB::table('m_kategori')
                    ->where('kode_divisi', $kodeDivisi)
                    ->where('kode_kategori', $kodeKategori)
                    ->update($data);
                $kategori->refresh();
                $kategori->load(['divisi']);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Kategori berhasil diperbarui',
                'data' => (new KategoriResource($kategori))->resolve($request),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui kategori',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $kodeDivisi, string $kodeKategori): JsonResponse
    {
        try {
            $kategori = Kategori::withCount(['barangs', 'dPakets'])
                ->where('kode_divisi', $kodeDivisi)
                ->where('kode_kategori', $kodeKategori)
                ->firstOrFail();
            
            if ($kategori->barangs_count > 0 || $kategori->d_pakets_count > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kategori tidak dapat dihapus karena masih memiliki data terkait',
                    'details' => [
                        'barangs_count' => $kategori->barangs_count,
                        'd_pakets_count' => $kategori->d_pakets_count,
                    ],
                ], 422);
            }

            \DB::table('m_kategori')
                ->where('kode_divisi', $kodeDivisi)
                ->where('kode_kategori', $kodeKategori)
                ->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Kategori berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            $status = $e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException ? 404 : 500;
            return response()->json([
                'success' => false,
                'message' => $status === 404 ? 'Kategori tidak ditemukan' : 'Gagal menghapus kategori',
                'error' => $e->getMessage(),
            ], $status);
        }
    }

    /**
     * Get statistics for categories in a specific division.
     */
    public function stats(string $kodeDivisi): JsonResponse
    {
        try {
            $stats = [
                'total_kategoris' => Kategori::where('kode_divisi', $kodeDivisi)->count(),
                'active_kategoris' => Kategori::where('kode_divisi', $kodeDivisi)->where('status', true)->count(),
                'inactive_kategoris' => Kategori::where('kode_divisi', $kodeDivisi)->where('status', false)->count(),
                'total_barangs' => \DB::table('m_barang')->where('kode_divisi', $kodeDivisi)->count(),
                'total_dpakets' => \DB::table('d_paket')->where('kode_divisi', $kodeDivisi)->count(),
            ];

            return response()->json([
                'success' => true,
                'data' => $stats,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil statistik kategori',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
