<?php

namespace App\Http\Controllers;

use App\Models\MResi;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MResiController extends Controller
{
    public function index(): JsonResponse
    {
        $mResis = MResi::with(['customer', 'penerimaanFinanceDetails'])->get();
        return response()->json($mResis);
    }

    public function create()
    {
        // Return view for create form if needed
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'no_resi' => 'required|string|max:20',
            'tgl_resi' => 'required|date',
            'kode_cust' => 'required|string|max:15',
            'nilai' => 'required|numeric|min:0',
            'status' => 'required|boolean'
        ]);

        $mResi = MResi::create($request->all());
        return response()->json($mResi, 201);
    }

    public function show(string $noResi): JsonResponse
    {
        $mResi = MResi::with(['customer', 'penerimaanFinanceDetails'])
            ->where('no_resi', $noResi)
            ->firstOrFail();
        return response()->json($mResi);
    }

    public function edit(string $noResi)
    {
        // Return view for edit form if needed
    }

    public function update(Request $request, string $noResi): JsonResponse
    {
        $request->validate([
            'tgl_resi' => 'required|date',
            'kode_cust' => 'required|string|max:15',
            'nilai' => 'required|numeric|min:0',
            'status' => 'required|boolean'
        ]);

        $mResi = MResi::where('no_resi', $noResi)
            ->firstOrFail();
        
        $mResi->update($request->all());
        return response()->json($mResi);
    }

    public function destroy(string $noResi): JsonResponse
    {
        $mResi = MResi::where('no_resi', $noResi)
            ->firstOrFail();
        
        $mResi->delete();
        return response()->json(['message' => 'MResi deleted successfully']);
    }
}
