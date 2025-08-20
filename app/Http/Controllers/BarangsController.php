<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DBarang;
use Illuminate\Support\Facades\Validator;

class BarangsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            // Ambil semua barang dengan sorting A-Z dan format tanggal yang benar
            $barangs = DBarang::orderBy('kodebarang', 'asc')->get();
            
            // Transform data to match frontend expectations - only show required fields
            $transformedBarangs = $barangs->map(function ($barang) {
                return [
                    'kode_barang' => $barang->kodebarang,
                    'modal' => (float) ($barang->modal ?? 0),
                    'stok' => (int) ($barang->stok ?? 0),
                    'tanggal_masuk' => $barang->tglmasuk ? date('Y-m-d', strtotime($barang->tglmasuk)) : null,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $transformedBarangs,
                'message' => 'Barangs retrieved successfully',
                'total_count' => $transformedBarangs->count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => 'Error retrieving barangs: ' . $e->getMessage(),
                'total_count' => 0
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'kode_barang' => 'required|string|max:50|unique:dbo.d_barang,kodebarang',
                'nama_barang' => 'nullable|string|max:255',
                'kodedivisi' => 'required|string|max:10',
                'modal' => 'required|numeric|min:0',
                'stok' => 'required|integer|min:0',
                'tanggal_masuk' => 'nullable|date'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'data' => null,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $barang = DBarang::create([
                'kodebarang' => $request->kode_barang,
                'kodedivisi' => $request->kodedivisi,
                'modal' => $request->modal,
                'stok' => $request->stok,
                'tglmasuk' => $request->tanggal_masuk ? $request->tanggal_masuk : now()
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'kode_barang' => $barang->kodebarang,
                    'modal' => (float) $barang->modal,
                    'stok' => (int) $barang->stok,
                    'tanggal_masuk' => $barang->tglmasuk ? date('Y-m-d', strtotime($barang->tglmasuk)) : null,
                ],
                'message' => 'Barang created successfully'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Error creating barang: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $barang = DBarang::where('id', $id)->first();

            if (!$barang) {
                return response()->json([
                    'success' => false,
                    'data' => null,
                    'message' => 'Barang not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'kode_barang' => $barang->kodebarang,
                    'modal' => (float) $barang->modal,
                    'stok' => (int) $barang->stok,
                    'tanggal_masuk' => $barang->tglmasuk ? date('Y-m-d', strtotime($barang->tglmasuk)) : null,
                ],
                'message' => 'Barang retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Error retrieving barang: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $barang = DBarang::where('id', $id)->first();

            if (!$barang) {
                return response()->json([
                    'success' => false,
                    'data' => null,
                    'message' => 'Barang not found'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'kode_barang' => 'required|string|max:50|unique:dbo.d_barang,kodebarang,' . $id . ',id',
                'nama_barang' => 'nullable|string|max:255',
                'kodedivisi' => 'required|string|max:10',
                'modal' => 'required|numeric|min:0',
                'stok' => 'required|integer|min:0',
                'tanggal_masuk' => 'nullable|date'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'data' => null,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $barang->update([
                'kodebarang' => $request->kode_barang,
                'kodedivisi' => $request->kodedivisi,
                'modal' => $request->modal,
                'stok' => $request->stok,
                'tglmasuk' => $request->tanggal_masuk ? $request->tanggal_masuk : $barang->tglmasuk
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'kode_barang' => $barang->kodebarang,
                    'modal' => (float) $barang->modal,
                    'stok' => (int) $barang->stok,
                    'tanggal_masuk' => $barang->tglmasuk ? date('Y-m-d', strtotime($barang->tglmasuk)) : null,
                ],
                'message' => 'Barang updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Error updating barang: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $barang = DBarang::where('id', $id)->first();

            if (!$barang) {
                return response()->json([
                    'success' => false,
                    'data' => null,
                    'message' => 'Barang not found'
                ], 404);
            }

            $barang->delete();

            return response()->json([
                'success' => true,
                'data' => null,
                'message' => 'Barang deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Error deleting barang: ' . $e->getMessage()
            ], 500);
        }
    }
}
