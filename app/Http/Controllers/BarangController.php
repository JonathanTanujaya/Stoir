<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MBarang;
use Illuminate\Support\Facades\DB;

class BarangController extends Controller
{
    /**
     * Display a listing of barang.
     */
    public function index(Request $request)
    {
        try {
            $query = MBarang::query();
            
            // Add search functionality
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('kodebarang', 'like', "%{$search}%");
                });
            }
            
            // Add division filter if provided
            if ($request->has('kodedivisi') && !empty($request->kodedivisi)) {
                $query->where('kodedivisi', $request->kodedivisi);
            }
            
            // Only items with stock
            $query->where('stok', '>', 0);
            
            $barang = $query->get();
            
            // Transform data
            $transformedBarang = $barang->map(function ($item) {
                return [
                    'kode' => $item->kodebarang,
                    'nama' => $item->kodebarang, // Using kodebarang as nama since no nama field
                    'kodedivisi' => $item->kodedivisi,
                    'harga' => (float)($item->modal ?? 0),
                    'stok' => (int)($item->stok ?? 0),
                    'tglmasuk' => $item->tglmasuk,
                    'id' => $item->id
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $transformedBarang,
                'message' => 'Barang retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => 'Error retrieving barang: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search barang by query
     */
    public function search(Request $request)
    {
        try {
            $query = $request->get('q', '');
            $kodedivisi = $request->get('kodedivisi', '01'); // Default division
            
            if (empty($query)) {
                return response()->json([
                    'success' => true,
                    'data' => [],
                    'message' => 'No search query provided'
                ]);
            }
            
            $barang = MBarang::where('kodedivisi', $kodedivisi)
                ->where('stok', '>', 0)
                ->where('kodebarang', 'like', "%{$query}%")
                ->get();
            
            $transformedBarang = $barang->map(function ($item) {
                return [
                    'kode' => $item->kodebarang,
                    'nama' => $item->kodebarang, // Using kodebarang as nama since no nama field
                    'kodedivisi' => $item->kodedivisi,
                    'harga' => (float)($item->modal ?? 0),
                    'stok' => (int)($item->stok ?? 0)
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $transformedBarang,
                'message' => 'Search results retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => 'Error searching barang: ' . $e->getMessage()
            ], 500);
        }
    }
}