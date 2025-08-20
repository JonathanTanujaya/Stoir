<?php

namespace App\Http\Controllers;

use App\Models\DBarang;
use Illuminate\Http\Request;

class BarangController extends Controller
{
    public function index()
    {
        try {
            // Ambil semua barang dengan sorting alphabetical berdasarkan kodebarang (nama barang)
            $barangs = DBarang::select(
                'id',
                'kodedivisi',
                'kodebarang as kode_barang',
                'kodebarang as nama_barang',  // kodebarang adalah nama barang
                'kodedivisi as kategori',
                'modal',
                'stok',
                'tglmasuk as tanggal_masuk'
            )
            ->orderBy('kodebarang', 'asc')  // Sort A-Z berdasarkan nama barang
            ->get();

            return response()->json([
                'success' => true,
                'data' => $barangs,
                'message' => 'Products retrieved successfully',
                'total_count' => $barangs->count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'data' => [],
                'total_count' => 0
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $barang = DBarang::create([
                'kodedivisi' => $request->kodedivisi,
                'kodebarang' => $request->kode_barang,
                'modal' => $request->modal,
                'stok' => $request->stok,
                'tglmasuk' => now()
            ]);

            return response()->json([
                'success' => true,
                'data' => $barang,
                'message' => 'Barang berhasil ditambahkan'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $barang = DBarang::find($id);
            
            if (!$barang) {
                return response()->json([
                    'success' => false,
                    'message' => 'Barang tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $barang
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $barang = DBarang::find($id);
            
            if (!$barang) {
                return response()->json([
                    'success' => false,
                    'message' => 'Barang tidak ditemukan'
                ], 404);
            }

            $barang->update([
                'kodedivisi' => $request->kodedivisi,
                'kodebarang' => $request->kode_barang,
                'modal' => $request->modal,
                'stok' => $request->stok
            ]);

            return response()->json([
                'success' => true,
                'data' => $barang,
                'message' => 'Barang berhasil diupdate'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $barang = DBarang::find($id);
            
            if (!$barang) {
                return response()->json([
                    'success' => false,
                    'message' => 'Barang tidak ditemukan'
                ], 404);
            }

            $barang->delete();

            return response()->json([
                'success' => true,
                'message' => 'Barang berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    // Method untuk menampilkan barang berdasarkan divisi dengan sorting
    public function showByDivisi($kodeDivisi)
    {
        try {
            $barangs = DBarang::select(
                'id',
                'kodedivisi',
                'kodebarang as kode_barang',
                'kodebarang as nama_barang',
                'kodedivisi as kategori',
                'modal',
                'stok',
                'tglmasuk as tanggal_masuk'
            )
            ->where('kodedivisi', $kodeDivisi)
            ->orderBy('kodebarang', 'asc')  // Sort A-Z
            ->get();

            return response()->json([
                'success' => true,
                'data' => $barangs,
                'message' => "Products for division {$kodeDivisi} retrieved successfully",
                'total_count' => $barangs->count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'data' => [],
                'total_count' => 0
            ], 500);
        }
    }
}
