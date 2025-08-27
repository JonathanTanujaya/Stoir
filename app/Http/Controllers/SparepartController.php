<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SparepartController extends Controller
{
    /**
     * Display a listing of spareparts from d_barang table
     */
    public function index(Request $request)
    {
        try {
            $query = DB::table('dbo.d_barang')
                ->select(
                    'kodedivisi',
                    'kodebarang',
                    'tglmasuk',
                    'modal',
                    'stok',
                    'id'
                )
                ->orderBy('tglmasuk', 'desc')
                ->orderBy('kodebarang');

            // Apply filters if provided
            if ($request->has('kodedivisi') && $request->kodedivisi !== '') {
                $query->where('kodedivisi', $request->kodedivisi);
            }

            if ($request->has('kodebarang') && $request->kodebarang !== '') {
                $query->where('kodebarang', 'like', "%{$request->kodebarang}%");
            }

            if ($request->has('stok_min') && $request->stok_min !== '') {
                $query->where('stok', '>=', $request->stok_min);
            }

            // Pagination
            $perPage = $request->get('per_page', 20); // Change default to 20
            $currentPage = $request->get('page', 1);
            
            $totalCount = $query->count();
            
            // If per_page is 'all' or -1, return all data
            if ($perPage === 'all' || $perPage == -1) {
                $rows = $query->get();
                $currentPage = 1;
                $totalPages = 1;
            } else {
                $offset = ($currentPage - 1) * $perPage;
                $rows = $query->offset($offset)->limit($perPage)->get();
                $totalPages = ceil($totalCount / $perPage);
            }

            // Format data for API response
            $data = $rows->map(function ($r) {
                return [
                    'kodeDivisi' => $r->kodedivisi,
                    'kodeBarang' => $r->kodebarang,
                    'tglMasuk' => $r->tglmasuk,
                    'modal' => (float) $r->modal,
                    'stok' => (int) $r->stok,
                    'id' => (int) $r->id
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $data,
                'message' => 'Spareparts retrieved successfully',
                'totalCount' => $totalCount,
                'currentPage' => $currentPage,
                'perPage' => $perPage,
                'totalPages' => $totalPages
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving spareparts: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created sparepart
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kodeDivisi' => 'required|string|max:10',
            'kodeBarang' => 'required|string|max:50',
            'tglMasuk' => 'required|date',
            'modal' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Get next ID
            $nextId = DB::table('dbo.d_barang')
                ->where('kodedivisi', $request->kodeDivisi)
                ->where('kodebarang', $request->kodeBarang)
                ->max('id') + 1;

            if (!$nextId) {
                $nextId = 1;
            }

            $inserted = DB::table('dbo.d_barang')->insert([
                'kodedivisi' => $request->kodeDivisi,
                'kodebarang' => $request->kodeBarang,
                'tglmasuk' => $request->tglMasuk,
                'modal' => $request->modal,
                'stok' => $request->stok,
                'id' => $nextId
            ]);

            if ($inserted) {
                // Retrieve the created record
                $created = DB::table('dbo.d_barang')
                    ->where('kodedivisi', $request->kodeDivisi)
                    ->where('kodebarang', $request->kodeBarang)
                    ->where('id', $nextId)
                    ->first();

                return response()->json([
                    'success' => true,
                    'data' => [
                        'kodeDivisi' => $created->kodedivisi,
                        'kodeBarang' => $created->kodebarang,
                        'tglMasuk' => $created->tglmasuk,
                        'modal' => (float) $created->modal,
                        'stok' => (int) $created->stok,
                        'id' => (int) $created->id
                    ],
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
    public function show($kodeDivisi, $kodeBarang, $id)
    {
        try {
            $barang = DB::table('dbo.d_barang')
                ->where('kodedivisi', $kodeDivisi)
                ->where('kodebarang', $kodeBarang)
                ->where('id', $id)
                ->first();

            if (!$barang) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sparepart not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'kodeDivisi' => $barang->kodedivisi,
                    'kodeBarang' => $barang->kodebarang,
                    'tglMasuk' => $barang->tglmasuk,
                    'modal' => (float) $barang->modal,
                    'stok' => (int) $barang->stok,
                    'id' => (int) $barang->id
                ],
                'message' => 'Sparepart retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving sparepart: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified sparepart
     */
    public function update(Request $request, $kodeDivisi, $kodeBarang, $id)
    {
        $validator = Validator::make($request->all(), [
            'tglMasuk' => 'required|date',
            'modal' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $barang = DB::table('dbo.d_barang')
                ->where('kodedivisi', $kodeDivisi)
                ->where('kodebarang', $kodeBarang)
                ->where('id', $id)
                ->first();

            if (!$barang) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sparepart not found'
                ], 404);
            }

            DB::table('dbo.d_barang')
                ->where('kodedivisi', $kodeDivisi)
                ->where('kodebarang', $kodeBarang)
                ->where('id', $id)
                ->update([
                    'tglmasuk' => $request->tglMasuk,
                    'modal' => $request->modal,
                    'stok' => $request->stok
                ]);

            $updatedBarang = DB::table('dbo.d_barang')
                ->where('kodedivisi', $kodeDivisi)
                ->where('kodebarang', $kodeBarang)
                ->where('id', $id)
                ->first();

            return response()->json([
                'success' => true,
                'data' => [
                    'kodeDivisi' => $updatedBarang->kodedivisi,
                    'kodeBarang' => $updatedBarang->kodebarang,
                    'tglMasuk' => $updatedBarang->tglmasuk,
                    'modal' => (float) $updatedBarang->modal,
                    'stok' => (int) $updatedBarang->stok,
                    'id' => (int) $updatedBarang->id
                ],
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
    public function destroy($kodeDivisi, $kodeBarang, $id)
    {
        try {
            $barang = DB::table('dbo.d_barang')
                ->where('kodedivisi', $kodeDivisi)
                ->where('kodebarang', $kodeBarang)
                ->where('id', $id)
                ->first();

            if (!$barang) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sparepart not found'
                ], 404);
            }

            $deleted = DB::table('dbo.d_barang')
                ->where('kodedivisi', $kodeDivisi)
                ->where('kodebarang', $kodeBarang)
                ->where('id', $id)
                ->delete();

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
     * Search spareparts with filters
     */
    public function search(Request $request)
    {
        try {
            $query = DB::table('dbo.d_barang')
                ->select(
                    'kodedivisi',
                    'kodebarang',
                    'tglmasuk',
                    'modal',
                    'stok',
                    'id'
                );

            // Apply search filters
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('kodebarang', 'like', "%{$search}%")
                      ->orWhere('kodedivisi', 'like', "%{$search}%");
                });
            }

            if ($request->has('kodedivisi')) {
                $query->where('kodedivisi', $request->kodedivisi);
            }

            if ($request->has('stok_min')) {
                $query->where('stok', '>=', $request->stok_min);
            }

            if ($request->has('stok_max')) {
                $query->where('stok', '<=', $request->stok_max);
            }

            $rows = $query->orderBy('tglmasuk', 'desc')->get();

            $data = $rows->map(function ($r) {
                return [
                    'kodeDivisi' => $r->kodedivisi,
                    'kodeBarang' => $r->kodebarang,
                    'tglMasuk' => $r->tglmasuk,
                    'modal' => (float) $r->modal,
                    'stok' => (int) $r->stok,
                    'id' => (int) $r->id
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $data,
                'message' => 'Search completed successfully',
                'totalCount' => $data->count()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error searching spareparts: ' . $e->getMessage()
            ], 500);
        }
    }
}
