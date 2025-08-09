<?php

namespace App\Http\Controllers;

use App\Models\TmpPrintInvoice;
use Illuminate\Http\Request;

class TmpPrintInvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $tmpPrintInvoices = TmpPrintInvoice::orderBy('tglfaktur', 'desc')
                                               ->orderBy('noinvoice', 'desc')
                                               ->limit(10)
                                               ->get();
            
            return response()->json([
                'success' => true,
                'message' => 'Data tmp print invoices retrieved successfully',
                'data' => $tmpPrintInvoices,
                'total_shown' => $tmpPrintInvoices->count(),
                'note' => 'Showing latest 10 records for testing purposes'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve tmp print invoices data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'kodedivisi' => 'required|string|max:2',
                'noinvoice' => 'required|string|max:20',
                'tglprint' => 'required|date',
                'userid' => 'required|string|max:20',
                'status' => 'nullable|string|max:10'
            ]);

            $tmpPrintInvoice = TmpPrintInvoice::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Tmp print invoice created successfully',
                'data' => $tmpPrintInvoice
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create tmp print invoice',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $tmpPrintInvoice = TmpPrintInvoice::findOrFail($id);
            
            return response()->json([
                'success' => true,
                'message' => 'Tmp print invoice data retrieved successfully',
                'data' => $tmpPrintInvoice
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Tmp print invoice not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $tmpPrintInvoice = TmpPrintInvoice::findOrFail($id);
            
            $request->validate([
                'kodedivisi' => 'required|string|max:2',
                'noinvoice' => 'required|string|max:20',
                'tglprint' => 'required|date',
                'userid' => 'required|string|max:20',
                'status' => 'nullable|string|max:10'
            ]);

            $tmpPrintInvoice->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Tmp print invoice updated successfully',
                'data' => $tmpPrintInvoice
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update tmp print invoice',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $tmpPrintInvoice = TmpPrintInvoice::findOrFail($id);
            $tmpPrintInvoice->delete();

            return response()->json([
                'success' => true,
                'message' => 'Tmp print invoice deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete tmp print invoice',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
