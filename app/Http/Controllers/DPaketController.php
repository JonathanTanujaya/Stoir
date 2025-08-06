<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DPaket; // Import model

class DPaketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items = DPaket::all();
        return response()->json(['data' => $items]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'NamaPaket' => 'required|string|max:255',
            'Deskripsi' => 'nullable|string|max:255',
        ]);

        $item = DPaket::create($validatedData);

        return response()->json(['message' => 'Item created successfully', 'data' => $item], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $item = DPaket::findOrFail($id);
        return response()->json(['data' => $item]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $item = DPaket::findOrFail($id);

        $validatedData = $request->validate([
            'NamaPaket' => 'required|string|max:255',
            'Deskripsi' => 'nullable|string|max:255',
        ]);

        $item->update($validatedData);

        return response()->json(['message' => 'Item updated successfully', 'data' => $item]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $item = DPaket::findOrFail($id);
        $item->delete();

        return response()->json(['message' => 'Item deleted successfully']);
    }
}