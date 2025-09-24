<?php

namespace App\Http\Controllers;

use App\Models\ReturnSales;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ReturnSalesController extends Controller
{
    public function index(): JsonResponse
    {
        $returnSales = ReturnSales::with(['customer', 'invoice', 'returnSalesDetails'])->get();
        return response()->json($returnSales);
    }

    public function create()
    {
        // Return view for create form if needed
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'no_return_sales' => 'required|string|max:20',
            'tgl_return' => 'required|date',
            'kode_cust' => 'required|string|max:15',
            'no_invoice' => 'required|string|max:20',
            'nilai' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string|max:255'
        ]);

        $returnSales = ReturnSales::create($request->all());
        return response()->json($returnSales, 201);
    }

    public function show(string $noReturnSales): JsonResponse
    {
        $returnSales = ReturnSales::with(['customer', 'invoice', 'returnSalesDetails'])
            ->where('no_return_sales', $noReturnSales)
            ->firstOrFail();
        return response()->json($returnSales);
    }

    public function edit(string $noReturnSales)
    {
        // Return view for edit form if needed
    }

    public function update(Request $request, string $noReturnSales): JsonResponse
    {
        $request->validate([
            'tgl_return' => 'required|date',
            'kode_cust' => 'required|string|max:15',
            'no_invoice' => 'required|string|max:20',
            'nilai' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string|max:255'
        ]);

        $returnSales = ReturnSales::where('no_return_sales', $noReturnSales)
            ->firstOrFail();
        
        $returnSales->update($request->all());
        return response()->json($returnSales);
    }

    public function destroy(string $noReturnSales): JsonResponse
    {
        $returnSales = ReturnSales::where('no_return_sales', $noReturnSales)
            ->firstOrFail();
        
        $returnSales->delete();
        return response()->json(['message' => 'ReturnSales deleted successfully']);
    }
}
