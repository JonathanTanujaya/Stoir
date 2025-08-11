<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MKategori;
use Illuminate\Support\Facades\Validator;

class CategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $categories = MKategori::all();
            
            // Transform data to match frontend expectations (camelCase)
            $transformedCategories = $categories->map(function ($category) {
                return [
                    'id' => $category->kodekategori,
                    'namaKategori' => $category->kategori,
                    'kodeKategori' => $category->kodekategori,
                    'deskripsi' => $category->kategori . ' - ' . $category->kodedivisi,
                    'status' => $category->status ? 'Aktif' : 'Tidak Aktif',
                    'kodedivisi' => $category->kodedivisi
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $transformedCategories,
                'message' => 'Categories retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => 'Error retrieving categories: ' . $e->getMessage()
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
                'namaKategori' => 'required|string|max:255',
                'kodeKategori' => 'required|string|max:50|unique:dbo.m_kategori,kodekategori',
                'status' => 'in:Aktif,Tidak Aktif'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'data' => null,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $category = MKategori::create([
                'kodekategori' => $request->kodeKategori,
                'kodedivisi' => $request->kodeKategori, // Use same value
                'kategori' => $request->namaKategori,
                'status' => $request->status === 'Aktif'
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $category->kodekategori,
                    'namaKategori' => $category->kategori,
                    'kodeKategori' => $category->kodekategori,
                    'deskripsi' => $category->kategori,
                    'status' => $category->status ? 'Aktif' : 'Tidak Aktif'
                ],
                'message' => 'Category created successfully'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Error creating category: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $category = MKategori::where('kodekategori', $id)
                                 ->orWhere('kodedivisi', $id)
                                 ->first();

            if (!$category) {
                return response()->json([
                    'success' => false,
                    'data' => null,
                    'message' => 'Category not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $category->kodekategori ?? $category->kodedivisi,
                    'namaKategori' => $category->kategori,
                    'kodeKategori' => $category->kodekategori ?? $category->kodedivisi,
                    'deskripsi' => $category->kategori,
                    'status' => $category->status ? 'Aktif' : 'Tidak Aktif'
                ],
                'message' => 'Category retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Error retrieving category: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $category = MKategori::where('kodekategori', $id)
                                 ->orWhere('kodedivisi', $id)
                                 ->first();

            if (!$category) {
                return response()->json([
                    'success' => false,
                    'data' => null,
                    'message' => 'Category not found'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'namaKategori' => 'required|string|max:255',
                'kodeKategori' => 'required|string|max:50|unique:dbo.m_kategori,kodekategori,' . $id . ',kodekategori',
                'status' => 'in:Aktif,Tidak Aktif'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'data' => null,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $category->update([
                'kategori' => $request->namaKategori,
                'kodekategori' => $request->kodeKategori,
                'status' => $request->status === 'Aktif'
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $category->kodekategori,
                    'namaKategori' => $category->kategori,
                    'kodeKategori' => $category->kodekategori,
                    'deskripsi' => $category->kategori,
                    'status' => $category->status ? 'Aktif' : 'Tidak Aktif'
                ],
                'message' => 'Category updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Error updating category: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $category = MKategori::where('kodekategori', $id)
                                 ->orWhere('kodedivisi', $id)
                                 ->first();

            if (!$category) {
                return response()->json([
                    'success' => false,
                    'data' => null,
                    'message' => 'Category not found'
                ], 404);
            }

            $category->delete();

            return response()->json([
                'success' => true,
                'data' => null,
                'message' => 'Category deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Error deleting category: ' . $e->getMessage()
            ], 500);
        }
    }
}
