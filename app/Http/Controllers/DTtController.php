<?php

namespace App\Http\Controllers;

use App\Models\DTt;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DTtController extends Controller
{
    public function index(): JsonResponse
    {
        $dTts = DTt::with(['customer', 'invoice'])->get();
        return response()->json($dTts);
    }

    public function create()
    {
        // Return view for create form if needed
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'no_tt' => 'required|string|max:20',
            'no_invoice' => 'required|string|max:20',
            'tgl_invoice' => 'required|date',
            'kode_cust' => 'required|string|max:15',
            'nilai' => 'required|numeric|min:0'
        ]);

        $dTt = DTt::create($request->all());
        return response()->json($dTt, 201);
    }

    public function show(string $noTt, string $noInvoice): JsonResponse
    {
        $dTt = DTt::with(['customer', 'invoice'])
            ->where('no_tt', $noTt)
            ->where('no_invoice', $noInvoice)
            ->firstOrFail();
        return response()->json($dTt);
    }

    public function edit(string $noTt, string $noInvoice)
    {
        // Return view for edit form if needed
    }

    public function update(Request $request, string $noTt, string $noInvoice): JsonResponse
    {
        $request->validate([
            'tgl_invoice' => 'required|date',
            'kode_cust' => 'required|string|max:15',
            'nilai' => 'required|numeric|min:0'
        ]);

        $dTt = DTt::where('no_tt', $noTt)
            ->where('no_invoice', $noInvoice)
            ->firstOrFail();
        
        $dTt->update($request->all());
        return response()->json($dTt);
    }

    public function destroy(string $noTt, string $noInvoice): JsonResponse
    {
        $dTt = DTt::where('no_tt', $noTt)
            ->where('no_invoice', $noInvoice)
            ->firstOrFail();
        
        $dTt->delete();
        return response()->json(['message' => 'DTt deleted successfully']);
    }
}
