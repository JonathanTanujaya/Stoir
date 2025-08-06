<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MDokumen; // Asumsi model MDokumen ada

class MDokumenController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $mdokumens = MDokumen::all();
        return response()->json($mdokumens);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_dokumen' => 'required|string|max:255|unique:m_dokumens',
        ]);

        $mdokumen = MDokumen::create($request->all());
        return response()->json($mdokumen, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $mdokumen = MDokumen::findOrFail($id);
        return response()->json($mdokumen);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'nama_dokumen' => 'required|string|max:255|unique:m_dokumens,nama_dokumen,' . $id,
        ]);

        $mdokumen = MDokumen::findOrFail($id);
        $mdokumen->update($request->all());
        return response()->json($mdokumen);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $mdokumen = MDokumen::findOrFail($id);
        $mdokumen->delete();
        return response()->json(null, 204);
    }
}
