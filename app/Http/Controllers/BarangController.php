<?php
// File: app/Http/Controllers/BarangController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MasterBarang;
use App\Models\DetailBarang;
use Illuminate\Support\Facades\DB;

class BarangController extends Controller
{
    /**
     * Display a listing of barang with modern filtering and search
     */
    public function index(Request $request)
    {
        try {
            $query = MasterBarang::with(['detailBarang' => function($q) use ($request) {
                if ($request->has('kode_divisi') && !empty($request->kode_divisi)) {
                    $q->where('KODE_DIVISI', $request->kode_divisi);
                }
                $q->where('STOK', '>', 0); // Only items with stock
            }])->where('AKTIF', true);
            
            // Search functionality
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('KODE_BARANG', 'like', "%{$search}%")
                      ->orWhere('NAMA_BARANG', 'like', "%{$search}%")
                      ->orWhere('BARCODE', 'like', "%{$search}%");
                });
            }
            
            // Category filter
            if ($request->has('kategori') && !empty($request->kategori)) {
                $query->where('KATEGORI', $request->kategori);
            }
            
            $barang = $query->get();
            
            // Transform data untuk frontend
            $transformedBarang = $barang->map(function ($item) use ($request) {
                $stokTotal = $item->detailBarang->sum('STOK');
                
                // Jika ada filter divisi, ambil detail dari divisi tersebut
                $detailStok = null;
                if ($request->has('kode_divisi') && !empty($request->kode_divisi)) {
                    $detailStok = $item->detailBarang->first();
                }
                
                return [
                    'kode' => $item->KODE_BARANG,
                    'nama' => $item->NAMA_BARANG,
                    'satuan' => $item->SATUAN,
                    'kategori' => $item->KATEGORI,
                    'harga_beli' => (float)($item->HARGA_BELI ?? 0),
                    'harga_jual' => (float)($item->HARGA_JUAL ?? 0),
                    'diskon_max' => (float)($item->DISKON_MAX ?? 0),
                    'minimum_stok' => (int)($item->MINIMUM_STOK ?? 0),
                    'stok_total' => $stokTotal,
                    'stok_divisi' => $detailStok ? $detailStok->STOK : 0,
                    'modal_terakhir' => $detailStok ? (float)$detailStok->MODAL : 0,
                    'tgl_masuk_terakhir' => $detailStok ? $detailStok->formatted_tgl_masuk : '',
                    'barcode' => $item->BARCODE,
                    'aktif' => $item->AKTIF,
                    'keterangan' => $item->KETERANGAN
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Data barang retrieved successfully',
                'data' => $transformedBarang,
                'total' => $transformedBarang->count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve barang data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search barang for autocomplete/dropdown
     */
    public function search(Request $request)
    {
        try {
            $search = $request->get('search', '');
            $kodeDivisi = $request->get('kode_divisi');
            $limit = $request->get('limit', 50);

            $query = MasterBarang::with(['detailBarang' => function($q) use ($kodeDivisi) {
                if ($kodeDivisi) {
                    $q->where('KODE_DIVISI', $kodeDivisi);
                }
                $q->where('STOK', '>', 0);
            }])->where('AKTIF', true);

            if (!empty($search)) {
                $query->where(function($q) use ($search) {
                    $q->where('KODE_BARANG', 'like', "%{$search}%")
                      ->orWhere('NAMA_BARANG', 'like', "%{$search}%")
                      ->orWhere('BARCODE', 'like', "%{$search}%");
                });
            }

            $barang = $query->limit($limit)->get();

            // Filter only items that have stock in the specified division
            $filteredBarang = $barang->filter(function($item) {
                return $item->detailBarang->isNotEmpty();
            });

            $transformedBarang = $filteredBarang->map(function ($item) {
                $detailStok = $item->detailBarang->first();
                
                return [
                    'kode' => $item->KODE_BARANG,
                    'nama' => $item->NAMA_BARANG,
                    'satuan' => $item->SATUAN,
                    'harga_jual' => (float)($item->HARGA_JUAL ?? 0),
                    'stok' => $detailStok ? $detailStok->STOK : 0,
                    'modal' => $detailStok ? (float)$detailStok->MODAL : 0,
                    'diskon_max' => (float)($item->DISKON_MAX ?? 0),
                    'barcode' => $item->BARCODE
                ];
            })->values();

            return response()->json([
                'success' => true,
                'message' => 'Barang search results',
                'data' => $transformedBarang,
                'total' => $transformedBarang->count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to search barang',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get barang by barcode for quick scan
     */
    public function getByBarcode(Request $request)
    {
        try {
            $barcode = $request->get('barcode');
            $kodeDivisi = $request->get('kode_divisi');
            
            if (empty($barcode)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Barcode is required'
                ], 400);
            }

            $barang = MasterBarang::with(['detailBarang' => function($q) use ($kodeDivisi) {
                if ($kodeDivisi) {
                    $q->where('KODE_DIVISI', $kodeDivisi);
                }
                $q->where('STOK', '>', 0);
            }])
            ->where('BARCODE', $barcode)
            ->where('AKTIF', true)
            ->first();

            if (!$barang) {
                return response()->json([
                    'success' => false,
                    'message' => 'Barang dengan barcode tersebut tidak ditemukan'
                ], 404);
            }

            if ($barang->detailBarang->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Barang tidak memiliki stok di divisi yang dipilih'
                ], 404);
            }

            $detailStok = $barang->detailBarang->first();
            
            $result = [
                'kode' => $barang->KODE_BARANG,
                'nama' => $barang->NAMA_BARANG,
                'satuan' => $barang->SATUAN,
                'harga_jual' => (float)$barang->HARGA_JUAL,
                'stok' => $detailStok->STOK,
                'modal' => (float)$detailStok->MODAL,
                'diskon_max' => (float)$barang->DISKON_MAX,
                'barcode' => $barang->BARCODE
            ];

            return response()->json([
                'success' => true,
                'message' => 'Barang ditemukan',
                'data' => $result
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get barang by barcode',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get stock summary by division
     */
    public function getStockSummary(Request $request)
    {
        try {
            $kodeDivisi = $request->get('kode_divisi');
            
            $query = DB::table('D_BARANG as d')
                ->join('M_BARANG as m', 'd.KODE_BARANG', '=', 'm.KODE_BARANG')
                ->select(
                    'm.KATEGORI',
                    DB::raw('COUNT(DISTINCT d.KODE_BARANG) as total_items'),
                    DB::raw('SUM(d.STOK) as total_stok'),
                    DB::raw('SUM(d.STOK * d.MODAL) as total_nilai')
                )
                ->where('m.AKTIF', true);
                
            if ($kodeDivisi) {
                $query->where('d.KODE_DIVISI', $kodeDivisi);
            }
            
            $summary = $query->groupBy('m.KATEGORI')->get();

            return response()->json([
                'success' => true,
                'message' => 'Stock summary retrieved successfully',
                'data' => $summary
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get stock summary',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}