<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MBarang;
use Illuminate\Support\Facades\Validator;

class BarangsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $barangs = MBarang::all();
            
            // Transform data to match frontend expectations
            $transformedBarangs = $barangs->map(function ($barang) {
                return [
                    'id' => $barang->id,
                    'kode_barang' => $barang->kodebarang,
                    'nama_barang' => $barang->kodebarang, // Use kodebarang as name
                    'kodedivisi' => $barang->kodedivisi,
                    'kode_divisi' => $barang->kodedivisi,
                    'kategori' => 'General', // Default category
                    'modal' => (float) ($barang->modal ?? 0),
                    'stok' => (int) ($barang->stok ?? 0),
                    'tanggal_masuk' => $barang->tglmasuk ? $barang->tglmasuk->format('Y-m-d') : null,
                    'status' => 'aktif'
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $transformedBarangs,
                'message' => 'Barangs retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => 'Error retrieving barangs: ' . $e->getMessage()
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

            $barang = MBarang::create([
                'kodebarang' => $request->kode_barang,
                'namabarang' => $request->nama_barang,
                'kodedivisi' => $request->kodedivisi,
                'modal' => $request->modal,
                'stok' => $request->stok,
                'tglmasuk' => $request->tanggal_masuk ? now() : null
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $barang->id,
                    'kode_barang' => $barang->kodebarang,
                    'nama_barang' => $barang->namabarang,
                    'kodedivisi' => $barang->kodedivisi,
                    'modal' => (float) $barang->modal,
                    'stok' => (int) $barang->stok,
                    'tanggal_masuk' => $barang->tglmasuk ? $barang->tglmasuk->format('Y-m-d') : null,
                    'status' => 'aktif'
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
            $barang = MBarang::with(['kategori'])->where('id', $id)->first();

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
                    'id' => $barang->id,
                    'kode_barang' => $barang->kodebarang,
                    'nama_barang' => $barang->namabarang,
                    'kodedivisi' => $barang->kodedivisi,
                    'kategori' => $barang->kategori->kategori ?? 'N/A',
                    'modal' => (float) $barang->modal,
                    'stok' => (int) $barang->stok,
                    'tanggal_masuk' => $barang->tglmasuk ? $barang->tglmasuk->format('Y-m-d') : null,
                    'status' => 'aktif'
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
            $barang = MBarang::where('id', $id)->first();

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
                'namabarang' => $request->nama_barang,
                'kodedivisi' => $request->kodedivisi,
                'modal' => $request->modal,
                'stok' => $request->stok,
                'tglmasuk' => $request->tanggal_masuk ? $request->tanggal_masuk : $barang->tglmasuk
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $barang->id,
                    'kode_barang' => $barang->kodebarang,
                    'nama_barang' => $barang->namabarang,
                    'kodedivisi' => $barang->kodedivisi,
                    'modal' => (float) $barang->modal,
                    'stok' => (int) $barang->stok,
                    'tanggal_masuk' => $barang->tglmasuk ? $barang->tglmasuk->format('Y-m-d') : null,
                    'status' => 'aktif'
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
            $barang = MBarang::where('id', $id)->first();

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
