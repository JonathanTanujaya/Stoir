<?php

namespace App\Http\Controllers;

use App\Models\MBarang;
use App\Models\DBarang;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class BarangController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {
            $barang = MBarang::all();
            return response()->json([
                'success' => true,
                'data' => $barang
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch barang',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'kodedivisi' => 'required|string|max:3',
                'kodebarang' => 'required|string|max:20',
                'namabarang' => 'required|string|max:100',
                'kodekategori' => 'nullable|string|max:10',
                'hargalist' => 'nullable|numeric',
                'hargajual' => 'nullable|numeric',
                'satuan' => 'nullable|string|max:10',
                'merk' => 'nullable|string|max:50',
                'disc1' => 'nullable|numeric',
                'disc2' => 'nullable|numeric',
                'barcode' => 'nullable|string|max:50',
                'status' => 'nullable|boolean',
                'lokasi' => 'nullable|string|max:50',
                'hargalist2' => 'nullable|numeric',
                'hargajual2' => 'nullable|numeric',
                'checklist' => 'nullable|boolean',
                'stokkmin' => 'nullable|integer'
            ]);

            // Check if combination already exists
            $exists = MBarang::where('kodedivisi', $validated['kodedivisi'])
                            ->where('kodebarang', $validated['kodebarang'])
                            ->exists();
            
            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Barang with this kodedivisi and kodebarang combination already exists'
                ], 422);
            }

            $barang = MBarang::create($validated);
            
            return response()->json([
                'success' => true,
                'message' => 'Barang created successfully',
                'data' => $barang
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
                'message' => 'Failed to create barang',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($kodeDivisi, $kodeBarang): JsonResponse
    {
        try {
            $barang = MBarang::where('kodedivisi', $kodeDivisi)
                            ->where('kodebarang', $kodeBarang)
                            ->first();
                            
            if (!$barang) {
                return response()->json([
                    'success' => false,
                    'message' => 'Barang not found'
                ], 404);
            }
            
            return response()->json([
                'success' => true,
                'data' => $barang
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch barang',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display barang by division code only.
     */
    public function showByDivisi($kodeDivisi): JsonResponse
    {
        try {
            $barang = MBarang::where('kodedivisi', $kodeDivisi)->get();
                            
            if ($barang->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No barang found for this division'
                ], 404);
            }
            
            return response()->json([
                'success' => true,
                'data' => $barang,
                'count' => $barang->count(),
                'message' => "Found {$barang->count()} barang for division {$kodeDivisi}"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch barang by division',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $kodeDivisi, $kodeBarang): JsonResponse
    {
        try {
            $barang = MBarang::where('kodedivisi', $kodeDivisi)
                            ->where('kodebarang', $kodeBarang)
                            ->first();
                            
            if (!$barang) {
                return response()->json([
                    'success' => false,
                    'message' => 'Barang not found'
                ], 404);
            }

            $validated = $request->validate([
                'namabarang' => 'required|string|max:100',
                'kodekategori' => 'nullable|string|max:10',
                'hargalist' => 'nullable|numeric',
                'hargajual' => 'nullable|numeric',
                'satuan' => 'nullable|string|max:10',
                'merk' => 'nullable|string|max:50',
                'disc1' => 'nullable|numeric',
                'disc2' => 'nullable|numeric',
                'barcode' => 'nullable|string|max:50',
                'status' => 'nullable|boolean',
                'lokasi' => 'nullable|string|max:50',
                'hargalist2' => 'nullable|numeric',
                'hargajual2' => 'nullable|numeric',
                'checklist' => 'nullable|boolean',
                'stokkmin' => 'nullable|integer'
            ]);

            $barang->update($validated);
            
            return response()->json([
                'success' => true,
                'message' => 'Barang updated successfully',
                'data' => $barang
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
                'message' => 'Failed to update barang',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($kodeDivisi, $kodeBarang): JsonResponse
    {
        try {
            $barang = MBarang::where('kodedivisi', $kodeDivisi)
                            ->where('kodebarang', $kodeBarang)
                            ->first();
                            
            if (!$barang) {
                return response()->json([
                    'success' => false,
                    'message' => 'Barang not found'
                ], 404);
            }

            $barang->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Barang deleted successfully'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete barang',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getVBarang()
    {
        try {
            // Simplified version - just return barang without join for now
            $vBarang = MBarang::limit(50)->get();

            return response()->json([
                'success' => true,
                'data' => $vBarang,
                'message' => 'VBarang endpoint working (simplified version)'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch VBarang',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
