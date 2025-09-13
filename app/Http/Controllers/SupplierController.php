<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSupplierRequest;
use App\Http\Requests\UpdateSupplierRequest;
use App\Http\Resources\SupplierResource;
use App\Http\Resources\SupplierCollection;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class SupplierController extends Controller
{
    /**
     * Display a listing of suppliers with pagination and filtering.
     */
    public function index(Request $request, string $kodeDivisi): JsonResponse
    {
        try {
            $request->attributes->set('query_start_time', microtime(true));
            
            $query = Supplier::query()
                ->where('kode_divisi', $kodeDivisi)
                ->with(['divisi']);

            // Apply search filter
            if ($request->filled('search')) {
                $search = $request->get('search');
                $query->where(function ($q) use ($search) {
                    $q->where('nama_supplier', 'ILIKE', "%{$search}%")
                      ->orWhere('kode_supplier', 'ILIKE', "%{$search}%")
                      ->orWhere('alamat', 'ILIKE', "%{$search}%")
                      ->orWhere('contact', 'ILIKE', "%{$search}%");
                });
            }

            // Apply status filter
            if ($request->filled('status')) {
                $query->where('status', $request->boolean('status'));
            }

            // Apply sorting
            $sortField = $request->get('sort', 'kode_supplier');
            $sortDirection = $request->get('direction', 'asc');
            
            $allowedSortFields = ['kode_supplier', 'nama_supplier', 'alamat', 'telp', 'contact'];
            if (in_array($sortField, $allowedSortFields)) {
                $query->orderBy($sortField, $sortDirection);
            }

            // Pagination
            $perPage = min($request->get('per_page', 15), 100);
            $suppliers = $query->paginate($perPage);

            return response()->json(new SupplierCollection($suppliers));

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data supplier',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created supplier.
     */
    public function store(StoreSupplierRequest $request, string $kodeDivisi)
    {
        try {
            DB::beginTransaction();

            $validated = $request->validated();
            $validated['kode_divisi'] = $kodeDivisi;

            $supplier = Supplier::create($validated);
            $supplier->load(['divisi']);

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => (new SupplierResource($supplier))->resolve($request),
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat supplier',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified supplier.
     */
    public function show(string $kodeDivisi, string $kodeSupplier)
    {
        try {
            $supplier = Supplier::where('kode_divisi', $kodeDivisi)
                ->where('kode_supplier', $kodeSupplier)
                ->with(['divisi'])
                ->firstOrFail();

            return response()->json([
                'success' => true,
                'data' => (new SupplierResource($supplier))->resolve(request()),
            ]);

        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
                return response()->json([
                    'success' => false,
                    'message' => 'Supplier tidak ditemukan',
                ], 404);
            }

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data supplier',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified supplier.
     */
    public function update(UpdateSupplierRequest $request, string $kodeDivisi, string $kodeSupplier)
    {
        try {
            DB::beginTransaction();
            
            $supplier = Supplier::where('kode_divisi', $kodeDivisi)
                ->where('kode_supplier', $kodeSupplier)
                ->firstOrFail();

            $validated = $request->validated();
            $supplier->update($validated);
            $supplier->load(['divisi']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Supplier berhasil diperbarui',
                'data' => (new SupplierResource($supplier))->resolve($request),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('Error updating supplier: ' . $e->getMessage(), [
                'kodeDivisi' => $kodeDivisi,
                'kodeSupplier' => $kodeSupplier,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui supplier',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified supplier.
     */
    public function destroy(string $kodeDivisi, string $kodeSupplier): JsonResponse
    {
        try {
            $supplier = Supplier::where('kode_divisi', $kodeDivisi)
                               ->where('kode_supplier', $kodeSupplier)
                               ->first();

            if (!$supplier) {
                return response()->json([
                    'success' => false,
                    'message' => 'Supplier tidak ditemukan'
                ], 404);
            }

            // Check if supplier has any part penerimaan
            $hasPartPenerimaan = $supplier->partPenerimaans()->exists();
            if ($hasPartPenerimaan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Supplier tidak dapat dihapus karena masih memiliki data penerimaan'
                ], 422);
            }

            DB::beginTransaction();
            
            // Manual delete to avoid composite key issues
            Supplier::where('kode_divisi', $kodeDivisi)
                    ->where('kode_supplier', $kodeSupplier)
                    ->delete();
                    
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Supplier berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus supplier',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get supplier statistics and summary.
     */
    public function getSupplierStats(string $kodeDivisi, string $kodeSupplier): JsonResponse
    {
        try {
            $supplier = Supplier::where('kode_divisi', $kodeDivisi)
                               ->where('kode_supplier', $kodeSupplier)
                               ->with(['partPenerimaans'])
                               ->first();

            if (!$supplier) {
                return response()->json([
                    'success' => false,
                    'message' => 'Supplier tidak ditemukan'
                ], 404);
            }

            $totalPenerimaan = $supplier->partPenerimaans()->count();
            $totalNilaiPenerimaan = $supplier->partPenerimaans()->sum('grand_total');
            $lastPenerimaan = $supplier->partPenerimaans()->latest('tgl_penerimaan')->first();

            return response()->json([
                'success' => true,
                'data' => [
                    'supplier_info' => [
                        'kode_supplier' => $supplier->kode_supplier,
                        'nama_supplier' => $supplier->nama_supplier
                    ],
                    'statistics' => [
                        'total_penerimaan' => $totalPenerimaan,
                        'total_nilai_penerimaan' => $totalNilaiPenerimaan,
                        'total_nilai_formatted' => 'Rp ' . number_format($totalNilaiPenerimaan, 0, ',', '.'),
                        'rata_rata_nilai' => $totalPenerimaan > 0 ? $totalNilaiPenerimaan / $totalPenerimaan : 0,
                        'last_penerimaan' => $lastPenerimaan ? $lastPenerimaan->tgl_penerimaan->format('Y-m-d') : null
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil statistik supplier',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
