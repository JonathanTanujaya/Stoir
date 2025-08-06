<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MCOA; // Asumsi model MCOA ada

class MCOAController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $mcoas = MCOA::all();
        return response()->json($mcoas);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'kode_coa' => 'required|string|max:255|unique:m_coas',
            'nama_coa' => 'required|string|max:255',
        ]);

        $mcoa = MCOA::create($request->all());
        return response()->json($mcoa, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $mcoa = MCOA::findOrFail($id);
        return response()->json($mcoa);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'kode_coa' => 'required|string|max:255|unique:m_coas,kode_coa,' . $id,
            'nama_coa' => 'required|string|max:255',
        ]);

        $mcoa = MCOA::findOrFail($id);
        $mcoa->update($request->all());
        return response()->json($mcoa);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $mcoa = MCOA::findOrFail($id);
        $mcoa->delete();
        return response()->json(null, 204);
    }
}
