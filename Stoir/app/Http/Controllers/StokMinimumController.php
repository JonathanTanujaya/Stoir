<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StokMinimum; // Import model

class StokMinimumController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items = StokMinimum::all();
        return response()->json(['data' => $items]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'KodeBarang' => 'required|string|max:255|unique:stok_minimum,KodeBarang',
            'StokMin' => 'required|integer|min:0',
        ]);

        $item = StokMinimum::create($validatedData);

        return response()->json(['message' => 'Item created successfully', 'data' => $item], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $item = StokMinimum::findOrFail($id);
        return response()->json(['data' => $item]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $item = StokMinimum::findOrFail($id);

        $validatedData = $request->validate([
            'KodeBarang' => 'required|string|max:255|unique:stok_minimum,KodeBarang,' . $id . ',ID',
            'StokMin' => 'required|integer|min:0',
        ]);

        $item->update($validatedData);

        return response()->json(['message' => 'Item updated successfully', 'data' => $item]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $item = StokMinimum::findOrFail($id);
        $item->delete();

        return response()->json(['message' => 'Item deleted successfully']);
    }
}