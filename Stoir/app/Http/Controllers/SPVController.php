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
        $items = SPV::all();
        return response()->json(['data' => $items]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'NoSPV' => 'required|string|max:255|unique:spv,NoSPV',
            'TglSPV' => 'required|date',
            'Keterangan' => 'nullable|string|max:255',
        ]);

        $item = SPV::create($validatedData);

        return response()->json(['message' => 'Item created successfully', 'data' => $item], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $item = SPV::findOrFail($id);
        return response()->json(['data' => $item]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $item = SPV::findOrFail($id);

        $validatedData = $request->validate([
            'NoSPV' => 'required|string|max:255|unique:spv,NoSPV,' . $id . ',ID',
            'TglSPV' => 'required|date',
            'Keterangan' => 'nullable|string|max:255',
        ]);

        $item->update($validatedData);

        return response()->json(['message' => 'Item updated successfully', 'data' => $item]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $item = SPV::findOrFail($id);
        $item->delete();

        return response()->json(['message' => 'Item deleted successfully']);
    }
}