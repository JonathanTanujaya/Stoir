<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserModule; // Import model

class UserModuleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items = UserModule::all();
        return response()->json(['data' => $items]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'UserID' => 'required|string|max:255',
            'ModuleID' => 'required|string|max:255',
        ]);

        $item = UserModule::create($validatedData);

        return response()->json(['message' => 'Item created successfully', 'data' => $item], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $item = UserModule::findOrFail($id);
        return response()->json(['data' => $item]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $item = UserModule::findOrFail($id);

        $validatedData = $request->validate([
            'UserID' => 'required|string|max:255',
            'ModuleID' => 'required|string|max:255',
        ]);

        $item->update($validatedData);

        return response()->json(['message' => 'Item updated successfully', 'data' => $item]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $item = UserModule::findOrFail($id);
        $item->delete();

        return response()->json(['message' => 'Item deleted successfully']);
    }
}