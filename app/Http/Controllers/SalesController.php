<?php

namespace App\Http\Controllers;

use App\Models\MSales;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SalesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $sales = MSales::all();
            return response()->json([
                'success' => true,
                'message' => 'Data sales retrieved successfully',
                'data' => $sales
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve sales data',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Show sales by divisi.
     */
    public function showByDivisi($kodeDivisi)
    {
        try {
            $sales = MSales::where('kodedivisi', $kodeDivisi)->get();
            
            if ($sales->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No sales found for this divisi',
                    'data' => []
                ], Response::HTTP_NOT_FOUND);
            }

            return response()->json([
                'success' => true,
                'message' => 'Sales by divisi retrieved successfully',
                'data' => $sales
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve sales by divisi',
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
                'kodesales' => 'required|string|max:50',
                'namasales' => 'required|string|max:100',
                'alamat' => 'nullable|string|max:200',
                'nohp' => 'nullable|string|max:20',
                'target' => 'nullable|numeric',
                'status' => 'boolean'
            ]);

            // Check if sales with this combination already exists
            $existingSales = MSales::findByCompositeKey(
                $validatedData['kodedivisi'],
                $validatedData['kodesales']
            );

            if ($existingSales) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sales with this kodedivisi and kodesales combination already exists'
                ], Response::HTTP_CONFLICT);
            }

            $sales = MSales::create($validatedData);

            return response()->json([
                'success' => true,
                'message' => 'Sales created successfully',
                'data' => $sales
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
                'message' => 'Failed to create sales',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($kodeDivisi, $kodeSales)
    {
        try {
            $sales = MSales::findByCompositeKey($kodeDivisi, $kodeSales);

            if (!$sales) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sales not found'
                ], Response::HTTP_NOT_FOUND);
            }

            return response()->json([
                'success' => true,
                'message' => 'Sales retrieved successfully',
                'data' => $sales
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve sales',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $kodeDivisi, $kodeSales)
    {
        try {
            $sales = MSales::findByCompositeKey($kodeDivisi, $kodeSales);

            if (!$sales) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sales not found'
                ], Response::HTTP_NOT_FOUND);
            }

            $validatedData = $request->validate([
                'namasales' => 'sometimes|required|string|max:100',
                'alamat' => 'sometimes|nullable|string|max:200',
                'nohp' => 'sometimes|nullable|string|max:20',
                'target' => 'sometimes|nullable|numeric',
                'status' => 'sometimes|boolean'
            ]);

            $sales->update($validatedData);

            return response()->json([
                'success' => true,
                'message' => 'Sales updated successfully',
                'data' => $sales->fresh()
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
                'message' => 'Failed to update sales',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($kodeDivisi, $kodeSales)
    {
        try {
            $sales = MSales::findByCompositeKey($kodeDivisi, $kodeSales);

            if (!$sales) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sales not found'
                ], Response::HTTP_NOT_FOUND);
            }

            $sales->delete();

            return response()->json([
                'success' => true,
                'message' => 'Sales deleted successfully'
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete sales',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
