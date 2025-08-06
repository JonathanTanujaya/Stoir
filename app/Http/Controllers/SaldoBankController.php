<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SaldoBank; // Import model

class SaldoBankController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items = SaldoBank::all();
        return response()->json(['data' => $items]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'KodeBank' => 'required|string|max:255',
            'Tanggal' => 'required|date',
            'SaldoAwal' => 'required|numeric',
        ]);

        $item = SaldoBank::create($validatedData);

        return response()->json(['message' => 'Item created successfully', 'data' => $item], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $item = SaldoBank::findOrFail($id);
        return response()->json(['data' => $item]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $item = SaldoBank::findOrFail($id);

        $validatedData = $request->validate([
            'KodeBank' => 'required|string|max:255',
            'Tanggal' => 'required|date',
            'SaldoAwal' => 'required|numeric',
        ]);

        $item->update($validatedData);

        return response()->json(['message' => 'Item updated successfully', 'data' => $item]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $item = SaldoBank::findOrFail($id);
        $item->delete();

        return response()->json(['message' => 'Item deleted successfully']);
    }
}