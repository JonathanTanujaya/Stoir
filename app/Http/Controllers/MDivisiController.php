<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MDivisi; // Asumsi model MDivisi ada

class MDivisiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $mdivisis = MDivisi::all();
        return response()->json($mdivisis);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_divisi' => 'required|string|max:255|unique:m_divisis',
        ]);

        $mdivisi = MDivisi::create($request->all());
        return response()->json($mdivisi, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $mdivisi = MDivisi::findOrFail($id);
        return response()->json($mdivisi);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'nama_divisi' => 'required|string|max:255|unique:m_divisis,nama_divisi,' . $id,
        ]);

        $mdivisi = MDivisi::findOrFail($id);
        $mdivisi->update($request->all());
        return response()->json($mdivisi);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $mdivisi = MDivisi::findOrFail($id);
        $mdivisi->delete();
        return response()->json(null, 204);
    }
}
