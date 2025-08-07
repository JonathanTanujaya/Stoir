<?php

namespace App\Http\Controllers;

use App\Models\MCust;
use App\Models\Invoice;
use App\Models\ReturnSales;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {
            $customers = MCust::all();
            return response()->json([
                'success' => true,
                'data' => $customers
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch customers',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'kodedivisi' => 'required|string|max:4',
                'kodecust' => 'required|string|max:10',
                'namacust' => 'required|string|max:50',
                'alamat' => 'nullable|string',
                'telp' => 'nullable|string|max:15',
                'creditlimit' => 'nullable|numeric',
                'status' => 'nullable|boolean'
            ]);

            $customer = MCust::create($validated);
            
            return response()->json([
                'success' => true,
                'message' => 'Customer created successfully',
                'data' => $customer
            ], 201);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create customer',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($kodeDivisi, $kodeCust): JsonResponse
    {
        try {
            $customer = MCust::where('kodedivisi', $kodeDivisi)
                            ->where('kodecust', $kodeCust)
                            ->first();
                            
            if (!$customer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Customer not found'
                ], 404);
            }
            
            return response()->json([
                'success' => true,
                'data' => $customer
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch customer',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display customers by division code only.
     */
    public function showByDivisi($kodeDivisi): JsonResponse
    {
        try {
            $customers = MCust::where('kodedivisi', $kodeDivisi)->get();
                            
            if ($customers->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No customers found for this division'
                ], 404);
            }
            
            return response()->json([
                'success' => true,
                'data' => $customers,
                'count' => $customers->count(),
                'message' => "Found {$customers->count()} customers for division {$kodeDivisi}"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch customers by division',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $kodeDivisi, $kodeCust): JsonResponse
    {
        try {
            $customer = MCust::where('kodedivisi', $kodeDivisi)
                            ->where('kodecust', $kodeCust)
                            ->first();
                            
            if (!$customer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Customer not found'
                ], 404);
            }

            $validated = $request->validate([
                'NamaCust' => 'required|string|max:50',
                'Alamat' => 'nullable|string',
                'Telp' => 'nullable|string|max:15',
                'CreditLimit' => 'nullable|numeric',
                'Status' => 'nullable|boolean'
            ]);

            $customer->update($validated);
            
            return response()->json([
                'success' => true,
                'message' => 'Customer updated successfully',
                'data' => $customer
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update customer',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($kodeDivisi, $kodeCust): JsonResponse
    {
        try {
            $customer = MCust::where('kodedivisi', $kodeDivisi)
                            ->where('kodecust', $kodeCust)
                            ->first();
                            
            if (!$customer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Customer not found'
                ], 404);
            }

            $customer->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Customer deleted successfully'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete customer',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getNamaCust(Request $request)
    {
        $kodedivisi = $request->input('kodedivisi');
        $noref = $request->input('noref');
        $tipe = $request->input('tipe');

        $nama = null;

        if ($tipe === 'penjualan') {
            $invoice = Invoice::where('KodeDivisi', $kodedivisi)->where('NoInvoice', $noref)->first();
            if ($invoice) {
                $customer = MCust::where('KodeDivisi', $kodedivisi)->where('KodeCust', $invoice->KodeCust)->first();
                if ($customer) {
                    $nama = $customer->NamaCust;
                }
            }
        } elseif ($tipe === 'Retur Penjualan') {
            $returnSales = ReturnSales::where('KodeDivisi', $kodedivisi)->where('NoRetur', $noref)->first();
            if ($returnSales) {
                $customer = MCust::where('KodeDivisi', $kodedivisi)->where('KodeCust', $returnSales->KodeCust)->first();
                if ($customer) {
                    $nama = $customer->NamaCust;
                }
            }
        }

        return response()->json(['nama' => $nama]);
    }
}
