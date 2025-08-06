<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MVoucher; // Import model

class MVoucherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items = MVoucher::all();
        return response()->json(['data' => $items]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'KodeVoucher' => 'required|string|max:255|unique:m_voucher,KodeVoucher',
            'NamaVoucher' => 'required|string|max:255',
            'Nilai' => 'required|numeric|min:0',
        ]);

        $item = MVoucher::create($validatedData);

        return response()->json(['message' => 'Item created successfully', 'data' => $item], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $item = MVoucher::findOrFail($id);
        return response()->json(['data' => $item]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $item = MVoucher::findOrFail($id);

        $validatedData = $request->validate([
            'KodeVoucher' => 'required|string|max:255|unique:m_voucher,KodeVoucher,' . $id . ',ID',
            'NamaVoucher' => 'required|string|max:255',
            'Nilai' => 'required|numeric|min:0',
        ]);

        $item->update($validatedData);

        return response()->json(['message' => 'Item updated successfully', 'data' => $item]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $item = MVoucher::findOrFail($id);
        $item->delete();

        return response()->json(['message' => 'Item deleted successfully']);
    }
}