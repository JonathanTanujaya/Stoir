<?php

namespace App\Http\Controllers;

use App\Models\MKategori;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class KategoriController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $kategori = MKategori::all();
            return response()->json([
                'success' => true,
                'message' => 'Data kategori retrieved successfully',
                'data' => $kategori
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve kategori data',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Show kategori by divisi.
     */
    public function showByDivisi($kodeDivisi)
    {
        try {
            $kategori = MKategori::where('kodedivisi', $kodeDivisi)->get();
            
            if ($kategori->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No kategori found for this divisi',
                    'data' => []
                ], Response::HTTP_NOT_FOUND);
            }

            return response()->json([
                'success' => true,
                'message' => 'Kategori by divisi retrieved successfully',
                'data' => $kategori
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve kategori by divisi',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'kodedivisi' => 'required|string|max:50',
                'kodekategori' => 'required|string|max:50',
                'kategori' => 'required|string|max:100',
                'status' => 'boolean'
            ]);

            // Check if kategori with this combination already exists
            $existingKategori = MKategori::findByCompositeKey(
                $validatedData['kodedivisi'],
                $validatedData['kodekategori']
            );

            if ($existingKategori) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kategori with this kodedivisi and kodekategori combination already exists'
                ], Response::HTTP_CONFLICT);
            }

            $kategori = MKategori::create($validatedData);

            return response()->json([
                'success' => true,
                'message' => 'Kategori created successfully',
                'data' => $kategori
            ], Response::HTTP_CREATED);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create kategori',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($kodeDivisi, $kodeKategori)
    {
        try {
            $kategori = MKategori::findByCompositeKey($kodeDivisi, $kodeKategori);

            if (!$kategori) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kategori not found'
                ], Response::HTTP_NOT_FOUND);
            }

            return response()->json([
                'success' => true,
                'message' => 'Kategori retrieved successfully',
                'data' => $kategori
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve kategori',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $kodeDivisi, $kodeKategori)
    {
        try {
            $kategori = MKategori::findByCompositeKey($kodeDivisi, $kodeKategori);

            if (!$kategori) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kategori not found'
                ], Response::HTTP_NOT_FOUND);
            }

            $validatedData = $request->validate([
                'kategori' => 'sometimes|required|string|max:100',
                'status' => 'sometimes|boolean'
            ]);

            $kategori->update($validatedData);

            return response()->json([
                'success' => true,
                'message' => 'Kategori updated successfully',
                'data' => $kategori->fresh()
            ], Response::HTTP_OK);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update kategori',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($kodeDivisi, $kodeKategori)
    {
        try {
            $kategori = MKategori::findByCompositeKey($kodeDivisi, $kodeKategori);

            if (!$kategori) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kategori not found'
                ], Response::HTTP_NOT_FOUND);
            }

            $kategori->delete();

            return response()->json([
                'success' => true,
                'message' => 'Kategori deleted successfully'
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete kategori',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
