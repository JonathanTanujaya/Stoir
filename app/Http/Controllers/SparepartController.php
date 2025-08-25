<?php

namespace App\Http\Controllers;

use App\Models\MBarang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SparepartController extends Controller
{
    /**
     * Display a listing of spareparts (from m_barang table)
     */
    public function index()
    {
        try {
            // Return dummy data for now - will be replaced with real data later
            $dummyData = [
                [
                    'kode_divisi' => '01',
                    'kode_barang' => 'SP001',
                    'nama_barang' => 'Oli Mesin Yamalube 10W-40',
                    'kode_kategori' => 'OLI',
                    'harga_list' => 45000,
                    'harga_jual' => 50000,
                    'satuan' => 'Botol',
                    'merk' => 'Yamaha',
                    'lokasi' => 'Rak A1',
                    'aktif' => true
                ],
                [
                    'kode_divisi' => '01',
                    'kode_barang' => 'SP002',
                    'nama_barang' => 'Ban Dalam 90/90-14',
                    'kode_kategori' => 'BAN',
                    'harga_list' => 25000,
                    'harga_jual' => 30000,
                    'satuan' => 'Pcs',
                    'merk' => 'IRC',
                    'lokasi' => 'Rak B2',
                    'aktif' => true
                ],
                [
                    'kode_divisi' => '01',
                    'kode_barang' => 'SP003',
                    'nama_barang' => 'Kampas Rem Depan Vario',
                    'kode_kategori' => 'REM',
                    'harga_list' => 35000,
                    'harga_jual' => 40000,
                    'satuan' => 'Set',
                    'merk' => 'TDR',
                    'lokasi' => 'Rak C1',
                    'aktif' => true
                ],
                [
                    'kode_divisi' => '01',
                    'kode_barang' => 'SP004',
                    'nama_barang' => 'Busi NGK Iridium',
                    'kode_kategori' => 'ELK',
                    'harga_list' => 65000,
                    'harga_jual' => 75000,
                    'satuan' => 'Pcs',
                    'merk' => 'NGK',
                    'lokasi' => 'Rak D3',
                    'aktif' => true
                ],
                [
                    'kode_divisi' => '01',
                    'kode_barang' => 'SP005',
                    'nama_barang' => 'Filter Udara Vario 125',
                    'kode_kategori' => 'FLT',
                    'harga_list' => 45000,
                    'harga_jual' => 55000,
                    'satuan' => 'Pcs',
                    'merk' => 'Honda',
                    'lokasi' => 'Rak E1',
                    'aktif' => true
                ],
                [
                    'kode_divisi' => '01',
                    'kode_barang' => 'SP006',
                    'nama_barang' => 'Rantai Motor 428H',
                    'kode_kategori' => 'RNT',
                    'harga_list' => 125000,
                    'harga_jual' => 150000,
                    'satuan' => 'Pcs',
                    'merk' => 'DID',
                    'lokasi' => 'Rak F2',
                    'aktif' => true
                ],
                [
                    'kode_divisi' => '01',
                    'kode_barang' => 'SP007',
                    'nama_barang' => 'Gear Set Vario 150',
                    'kode_kategori' => 'GER',
                    'harga_list' => 85000,
                    'harga_jual' => 100000,
                    'satuan' => 'Set',
                    'merk' => 'Honda',
                    'lokasi' => 'Rak G1',
                    'aktif' => true
                ],
                [
                    'kode_divisi' => '01',
                    'kode_barang' => 'SP008',
                    'nama_barang' => 'Lampu LED H4',
                    'kode_kategori' => 'ELK',
                    'harga_list' => 75000,
                    'harga_jual' => 90000,
                    'satuan' => 'Pcs',
                    'merk' => 'Osram',
                    'lokasi' => 'Rak H3',
                    'aktif' => true
                ],
                [
                    'kode_divisi' => '01',
                    'kode_barang' => 'SP009',
                    'nama_barang' => 'Velg Racing 17 inch',
                    'kode_kategori' => 'VLG',
                    'harga_list' => 450000,
                    'harga_jual' => 500000,
                    'satuan' => 'Pcs',
                    'merk' => 'SSR',
                    'lokasi' => 'Rak I1',
                    'aktif' => true
                ],
                [
                    'kode_divisi' => '01',
                    'kode_barang' => 'SP010',
                    'nama_barang' => 'Shock Breaker Belakang',
                    'kode_kategori' => 'SUS',
                    'harga_list' => 285000,
                    'harga_jual' => 320000,
                    'satuan' => 'Pcs',
                    'merk' => 'KYB',
                    'lokasi' => 'Rak J2',
                    'aktif' => true
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => $dummyData,
                'message' => 'Spareparts retrieved successfully',
                'total_count' => count($dummyData)
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

    /**
     * Store a newly created sparepart
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kode_divisi' => 'required|string|max:10',
            'kode_barang' => 'required|string|max:50',
            'nama_barang' => 'required|string|max:255',
            'kode_kategori' => 'nullable|string|max:50',
            'harga_list' => 'nullable|numeric|min:0',
            'harga_jual' => 'nullable|numeric|min:0',
            'satuan' => 'nullable|string|max:20',
            'merk' => 'nullable|string|max:100',
            'diskon' => 'nullable|numeric|min:0|max:100',
            'lokasi' => 'nullable|string|max:100'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Insert into database using query builder
            $inserted = DB::table('dbo.m_barang')->insert([
                'KodeDivisi' => $request->kode_divisi,
                'KodeBarang' => $request->kode_barang,
                'NamaBarang' => $request->nama_barang,
                'KodeKategori' => $request->kode_kategori,
                'HargaList' => $request->harga_list ?? 0,
                'HargaJual' => $request->harga_jual ?? 0,
                'Satuan' => $request->satuan,
                'merk' => $request->merk,
                'Lokasi' => $request->lokasi,
                'status' => true,
                'Disc1' => $request->diskon ?? 0,
                'Disc2' => 0,
                'HargaList2' => 0,
                'HargaJual2' => 0,
                'Barcode' => '',
                'StokMin' => 0,
                'Checklist' => false
            ]);

            if ($inserted) {
                $newBarang = [
                    'kode_divisi' => $request->kode_divisi,
                    'kode_barang' => $request->kode_barang,
                    'nama_barang' => $request->nama_barang,
                    'kode_kategori' => $request->kode_kategori,
                    'harga_list' => $request->harga_list ?? 0,
                    'harga_jual' => $request->harga_jual ?? 0,
                    'satuan' => $request->satuan,
                    'merk' => $request->merk,
                    'lokasi' => $request->lokasi,
                    'aktif' => true
                ];

                return response()->json([
                    'success' => true,
                    'data' => $newBarang,
                    'message' => 'Sparepart created successfully'
                ], 201);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create sparepart'
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating sparepart: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified sparepart
     */
    public function show($kodeDivisi, $kodeBarang)
    {
        try {
            $barang = DB::table('dbo.m_barang')
                ->select(
                    'KodeDivisi as kode_divisi',
                    'KodeBarang as kode_barang',
                    'NamaBarang as nama_barang',
                    'KodeKategori as kode_kategori',
                    'HargaList as harga_list',
                    'HargaJual as harga_jual',
                    'Satuan as satuan',
                    'merk',
                    'Lokasi as lokasi',
                    'status as aktif'
                )
                ->where('KodeDivisi', $kodeDivisi)
                ->where('KodeBarang', $kodeBarang)
                ->first();

            if (!$barang) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sparepart not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $barang,
                'message' => 'Sparepart retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified sparepart
     */
    public function update(Request $request, $kodeDivisi, $kodeBarang)
    {
        $validator = Validator::make($request->all(), [
            'nama_barang' => 'required|string|max:255',
            'kode_kategori' => 'nullable|string|max:50',
            'harga_list' => 'nullable|numeric|min:0',
            'harga_jual' => 'nullable|numeric|min:0',
            'satuan' => 'nullable|string|max:20',
            'merk' => 'nullable|string|max:100',
            'diskon' => 'nullable|numeric|min:0|max:100',
            'lokasi' => 'nullable|string|max:100'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $barang = DB::table('dbo.m_barang')
                ->where('KodeDivisi', $kodeDivisi)
                ->where('KodeBarang', $kodeBarang)
                ->first();

            if (!$barang) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sparepart not found'
                ], 404);
            }

            $updated = DB::table('dbo.m_barang')
                ->where('KodeDivisi', $kodeDivisi)
                ->where('KodeBarang', $kodeBarang)
                ->update([
                    'NamaBarang' => $request->nama_barang,
                    'KodeKategori' => $request->kode_kategori,
                    'HargaJual' => $request->harga_jual ?? 0,
                    'HargaList' => $request->harga_list ?? 0,
                    'Satuan' => $request->satuan,
                    'merk' => $request->merk,
                    'Lokasi' => $request->lokasi
                ]);

            return response()->json([
                'success' => true,
                'data' => $barang,
                'message' => 'Sparepart updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating sparepart: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified sparepart
     */
    public function destroy($kodeDivisi, $kodeBarang)
    {
        try {
            $barang = DB::table('dbo.m_barang')
                ->where('KodeDivisi', $kodeDivisi)
                ->where('KodeBarang', $kodeBarang)
                ->first();

            if (!$barang) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sparepart not found'
                ], 404);
            }

            // Soft delete - set status to false instead of actual delete
            $deleted = DB::table('dbo.m_barang')
                ->where('KodeDivisi', $kodeDivisi)
                ->where('KodeBarang', $kodeBarang)
                ->update(['status' => false]);

            return response()->json([
                'success' => true,
                'message' => 'Sparepart deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting sparepart: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search spareparts
     */
    public function search(Request $request)
    {
        try {
            $query = DB::table('dbo.m_barang')->where('status', true);

            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('KodeBarang', 'like', "%{$search}%")
                      ->orWhere('NamaBarang', 'like', "%{$search}%")
                      ->orWhere('KodeKategori', 'like', "%{$search}%")
                      ->orWhere('merk', 'like', "%{$search}%");
                });
            }

            if ($request->has('kategori')) {
                $query->where('KodeKategori', $request->kategori);
            }

            $barangs = $query->select(
                    'KodeDivisi as kode_divisi',
                    'KodeBarang as kode_barang',
                    'NamaBarang as nama_barang',
                    'KodeKategori as kode_kategori',
                    'HargaList as harga_list',
                    'HargaJual as harga_jual',
                    'Satuan as satuan',
                    'merk',
                    'Lokasi as lokasi',
                    'status as aktif'
                )
                ->orderBy('NamaBarang', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $barangs,
                'message' => 'Search completed successfully',
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
