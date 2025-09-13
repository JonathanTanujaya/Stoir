<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDivisiRequest;
use App\Http\Requests\UpdateDivisiRequest;
use App\Http\Resources\DivisiCollection;
use App\Http\Resources\DivisiResource;
use App\Models\Divisi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DivisiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Divisi::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode_divisi', 'LIKE', "%{$search}%")
                  ->orWhere('nama_divisi', 'LIKE', "%{$search}%");
            });
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'kode_divisi');
        $sortOrder = $request->get('sort_order', 'asc');
        
        if (in_array($sortBy, ['kode_divisi', 'nama_divisi'])) {
            $query->orderBy($sortBy, $sortOrder);
        }

        // Load relationships for counts
        $query->withCount(['banks', 'areas', 'customers', 'sales', 'barangs', 'invoices', 'suppliers']);

        $divisis = $query->get();

        return response()->json(new DivisiCollection($divisis));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDivisiRequest $request): JsonResponse
    {
        try {
            $divisi = Divisi::create($request->validated());
            
            return response()->json([
                'message' => 'Divisi berhasil dibuat.',
                'data' => new DivisiResource($divisi),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal membuat divisi.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $kodeDivisi): JsonResponse
    {
        try {
            $divisi = Divisi::with(['banks', 'areas', 'customers', 'sales', 'barangs', 'invoices', 'suppliers'])
                ->findOrFail($kodeDivisi);
            
            return response()->json([
                'data' => new DivisiResource($divisi),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Divisi tidak ditemukan.',
                'error' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDivisiRequest $request, string $kodeDivisi): JsonResponse
    {
        try {
            $divisi = Divisi::findOrFail($kodeDivisi);
            $divisi->update($request->validated());
            
            return response()->json([
                'message' => 'Divisi berhasil diperbarui.',
                'data' => new DivisiResource($divisi),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal memperbarui divisi.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $kodeDivisi): JsonResponse
    {
        try {
            $divisi = Divisi::withCount(['banks', 'areas', 'customers', 'sales', 'barangs', 'invoices', 'suppliers'])
                ->findOrFail($kodeDivisi);
            
            // Check if divisi has related data
            $hasRelatedData = $divisi->banks_count > 0 || 
                             $divisi->areas_count > 0 || 
                             $divisi->customers_count > 0 || 
                             $divisi->sales_count > 0 || 
                             $divisi->barangs_count > 0 || 
                             $divisi->invoices_count > 0 || 
                             $divisi->suppliers_count > 0;

            if ($hasRelatedData) {
                return response()->json([
                    'message' => 'Divisi tidak dapat dihapus karena masih memiliki data terkait.',
                    'details' => [
                        'banks_count' => $divisi->banks_count,
                        'areas_count' => $divisi->areas_count,
                        'customers_count' => $divisi->customers_count,
                        'sales_count' => $divisi->sales_count,
                        'barangs_count' => $divisi->barangs_count,
                        'invoices_count' => $divisi->invoices_count,
                        'suppliers_count' => $divisi->suppliers_count,
                    ],
                ], 422);
            }

            $divisi->delete();
            
            return response()->json([
                'message' => 'Divisi berhasil dihapus.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal menghapus divisi.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get statistics for all divisions.
     */
    public function stats(): JsonResponse
    {
        try {
            $stats = [
                'total_divisi' => Divisi::count(),
                'total_banks' => \DB::table('m_bank')->count(),
                'total_areas' => \DB::table('m_area')->count(),
                'total_customers' => \DB::table('m_customer')->count(),
                'total_sales' => \DB::table('m_sales')->count(),
                'total_barangs' => \DB::table('m_barang')->count(),
                'total_invoices' => \DB::table('t_invoice')->count(),
                'total_suppliers' => \DB::table('m_supplier')->count(),
            ];

            return response()->json([
                'data' => $stats,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal mengambil statistik.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
