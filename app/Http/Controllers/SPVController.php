<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SPV; // Import model

class SPVController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $items = SPV::all()->map(fn($s)=>[
                'id' => $s->id,
                'noSpv' => $s->nospv,
                'tglSpv' => $s->tglspv,
                'keterangan' => $s->keterangan
            ]);
            
            return response()->json([
                'success' => true,
                'data' => $items,
                'totalCount' => $items->count(),
                'message' => 'SPV data retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve SPV data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'noSpv' => 'required|string|max:255|unique:spv,nospv',
                'tglSpv' => 'required|date',
                'keterangan' => 'nullable|string|max:255',
            ]);

            $item = SPV::create([
                'nospv' => $validatedData['noSpv'],
                'tglspv' => $validatedData['tglSpv'],
                'keterangan' => $validatedData['keterangan']
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $item->id,
                    'noSpv' => $item->nospv,
                    'tglSpv' => $item->tglspv,
                    'keterangan' => $item->keterangan
                ],
                'message' => 'SPV created successfully'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create SPV',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $item = SPV::findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $item->id,
                    'noSpv' => $item->nospv,
                    'tglSpv' => $item->tglspv,
                    'keterangan' => $item->keterangan
                ],
                'message' => 'SPV retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'SPV not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $item = SPV::findOrFail($id);

            $validatedData = $request->validate([
                'noSpv' => 'required|string|max:255|unique:spv,nospv,' . $id . ',id',
                'tglSpv' => 'required|date',
                'keterangan' => 'nullable|string|max:255',
            ]);

            $item->update([
                'nospv' => $validatedData['noSpv'],
                'tglspv' => $validatedData['tglSpv'],
                'keterangan' => $validatedData['keterangan']
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $item->id,
                    'noSpv' => $item->nospv,
                    'tglSpv' => $item->tglspv,
                    'keterangan' => $item->keterangan
                ],
                'message' => 'SPV updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update SPV',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $item = SPV::findOrFail($id);
            $item->delete();

            return response()->json([
                'success' => true,
                'message' => 'SPV deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete SPV',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}