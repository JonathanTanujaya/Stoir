<?php

namespace App\Http\Controllers;

use App\Models\MArea;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AreaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {
            $areas = MArea::all();
            return response()->json([
                'success' => true,
                'data' => $areas
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch areas',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'kodedivisi' => 'required|string|max:4',
                'kodearea' => 'required|string|max:10',
                'area' => 'nullable|string|max:50',
                'status' => 'nullable|boolean'
            ]);

            // Check if combination already exists
            $exists = MArea::where('kodedivisi', $validated['kodedivisi'])
                          ->where('kodearea', $validated['kodearea'])
                          ->exists();
            
            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Area with this kodedivisi and kodearea combination already exists'
                ], 422);
            }

            $area = MArea::create($validated);
            
            return response()->json([
                'success' => true,
                'message' => 'Area created successfully',
                'data' => $area
            ], 201);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create area',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($kodeDivisi, $kodeArea): JsonResponse
    {
        try {
            $area = MArea::where('kodedivisi', $kodeDivisi)
                        ->where('kodearea', $kodeArea)
                        ->first();
                            
            if (!$area) {
                return response()->json([
                    'success' => false,
                    'message' => 'Area not found'
                ], 404);
            }
            
            return response()->json([
                'success' => true,
                'data' => $area
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch area',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display areas by division code only.
     */
    public function showByDivisi($kodeDivisi): JsonResponse
    {
        try {
            $areas = MArea::where('kodedivisi', $kodeDivisi)->get();
                            
            if ($areas->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No areas found for this division'
                ], 404);
            }
            
            return response()->json([
                'success' => true,
                'data' => $areas,
                'count' => $areas->count(),
                'message' => "Found {$areas->count()} areas for division {$kodeDivisi}"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch areas by division',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $kodeDivisi, $kodeArea): JsonResponse
    {
        try {
            $area = MArea::where('kodedivisi', $kodeDivisi)
                        ->where('kodearea', $kodeArea)
                        ->first();
                            
            if (!$area) {
                return response()->json([
                    'success' => false,
                    'message' => 'Area not found'
                ], 404);
            }

            $validated = $request->validate([
                'area' => 'nullable|string|max:50',
                'status' => 'nullable|boolean'
            ]);

            $area->update($validated);
            
            return response()->json([
                'success' => true,
                'message' => 'Area updated successfully',
                'data' => $area
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update area',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($kodeDivisi, $kodeArea): JsonResponse
    {
        try {
            $area = MArea::where('kodedivisi', $kodeDivisi)
                        ->where('kodearea', $kodeArea)
                        ->first();
                            
            if (!$area) {
                return response()->json([
                    'success' => false,
                    'message' => 'Area not found'
                ], 404);
            }

            $area->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Area deleted successfully'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete area',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
