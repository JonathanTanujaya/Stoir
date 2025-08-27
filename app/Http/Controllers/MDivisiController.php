<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mdivisi; // Asumsi model Mdivisi ada

class MdivisiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items = Mdivisi::all()->map(fn($d)=>[
            'id' => $d->id,
            'namaDivisi' => $d->nama_divisi
        ]);
        return response()->json([
            'success' => true,
            'data' => $items,
            'totalCount' => $items->count()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_divisi' => 'required|string|max:255|unique:m_divisis',
        ]);

        $mdivisi = Mdivisi::create($request->all());
        return response()->json([
            'success' => true,
            'data' => [ 'id' => $mdivisi->id, 'namaDivisi' => $mdivisi->nama_divisi ],
            'message' => 'Divisi created'
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $mdivisi = Mdivisi::findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => [ 'id' => $mdivisi->id, 'namaDivisi' => $mdivisi->nama_divisi ]
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'nama_divisi' => 'required|string|max:255|unique:m_divisis,nama_divisi,' . $id,
        ]);

        $mdivisi = Mdivisi::findOrFail($id);
        $mdivisi->update($request->all());
        return response()->json([
            'success' => true,
            'data' => [ 'id' => $mdivisi->id, 'namaDivisi' => $mdivisi->nama_divisi ],
            'message' => 'Divisi updated'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $mdivisi = Mdivisi::findOrFail($id);
        $mdivisi->delete();
        return response()->json([
            'success' => true,
            'message' => 'Divisi deleted'
        ]);
    }
}
