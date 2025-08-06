<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StokClaim; // Import model

class StokClaimController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items = StokClaim::all();
        return response()->json(['data' => $items]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'NoStokClaim' => 'required|string|max:255|unique:stok_claim,NoStokClaim',
            'TglStokClaim' => 'required|date',
            'Keterangan' => 'nullable|string|max:255',
        ]);

        $item = StokClaim::create($validatedData);

        return response()->json(['message' => 'Item created successfully', 'data' => $item], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $item = StokClaim::findOrFail($id);
        return response()->json(['data' => $item]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $item = StokClaim::findOrFail($id);

        $validatedData = $request->validate([
            'NoStokClaim' => 'required|string|max:255|unique:stok_claim,NoStokClaim,' . $id . ',ID',
            'TglStokClaim' => 'required|date',
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
        $item = StokClaim::findOrFail($id);
        $item->delete();

        return response()->json(['message' => 'Item deleted successfully']);
    }
}