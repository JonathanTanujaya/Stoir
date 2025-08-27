<?php

namespace App\Http\Controllers;

use App\Models\MSupplier;
use App\Models\PartPenerimaan;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $suppliers = MSupplier::active()->get()->map(function ($s) {
                return [
                    'kodeDivisi' => $s->kodedivisi ?? $s->KodeDivisi ?? null,
                    'kodeSupplier' => $s->kodesupplier ?? $s->KodeSupplier ?? null,
                    'namaSupplier' => $s->namasupplier ?? $s->NamaSupplier ?? null,
                    'alamat' => $s->alamat,
                    'telepon' => $s->telp ?? $s->telepon ?? null,
                    'contact' => $s->contact,
                    'status' => $s->status
                ];
            });
            
            return response()->json([
                'success' => true,
                'message' => 'Data suppliers retrieved successfully',
                'data' => $suppliers
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve suppliers data',
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
                'kodesupplier' => 'required|string|max:10',
                'namasupplier' => 'required|string|max:100',
                'alamat' => 'nullable|string',
                'telp' => 'nullable|string',
                'contact' => 'nullable|string',
                'status' => 'boolean'
            ]);

            $supplier = MSupplier::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Supplier created successfully',
                'data' => $supplier
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
                'message' => 'Failed to create supplier',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display suppliers by division.
     */
    public function showByDivisi($kodeDivisi)
    {
        try {
            $suppliers = MSupplier::byDivisi($kodeDivisi)->active()->get()->map(function ($s) {
                return [
                    'kodeDivisi' => $s->kodedivisi ?? $s->KodeDivisi ?? null,
                    'kodeSupplier' => $s->kodesupplier ?? $s->KodeSupplier ?? null,
                    'namaSupplier' => $s->namasupplier ?? $s->NamaSupplier ?? null,
                    'alamat' => $s->alamat,
                    'telepon' => $s->telp ?? $s->telepon ?? null,
                    'contact' => $s->contact,
                    'status' => $s->status
                ];
            });
            
            return response()->json([
                'success' => true,
                'message' => 'Suppliers retrieved successfully',
                'data' => $suppliers
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve suppliers',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($kodeDivisi, $kodeSupplier)
    {
        try {
            $supplier = MSupplier::findByCompositeKey($kodeDivisi, $kodeSupplier);
            
            if (!$supplier) {
                return response()->json([
                    'success' => false,
                    'message' => 'Supplier not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Supplier retrieved successfully',
                'data' => [
                    'kodeDivisi' => $supplier->kodedivisi ?? $supplier->KodeDivisi,
                    'kodeSupplier' => $supplier->kodesupplier ?? $supplier->KodeSupplier,
                    'namaSupplier' => $supplier->namasupplier ?? $supplier->NamaSupplier,
                    'alamat' => $supplier->alamat,
                    'telepon' => $supplier->telp ?? $supplier->telepon,
                    'contact' => $supplier->contact,
                    'status' => $supplier->status
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve supplier',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $kodeDivisi, $kodeSupplier)
    {
        try {
            $supplier = MSupplier::findByCompositeKey($kodeDivisi, $kodeSupplier);
            
            if (!$supplier) {
                return response()->json([
                    'success' => false,
                    'message' => 'Supplier not found'
                ], 404);
            }

            $request->validate([
                'namasupplier' => 'required|string|max:100',
                'alamat' => 'nullable|string',
                'telp' => 'nullable|string',
                'contact' => 'nullable|string',
                'status' => 'boolean'
            ]);

            $supplier->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Supplier updated successfully',
                'data' => [
                    'kodeDivisi' => $supplier->kodedivisi ?? $supplier->KodeDivisi,
                    'kodeSupplier' => $supplier->kodesupplier ?? $supplier->KodeSupplier,
                    'namaSupplier' => $supplier->namasupplier ?? $supplier->NamaSupplier,
                    'alamat' => $supplier->alamat,
                    'telepon' => $supplier->telp ?? $supplier->telepon,
                    'contact' => $supplier->contact,
                    'status' => $supplier->status
                ]
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
                'message' => 'Failed to update supplier',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($kodeDivisi, $kodeSupplier)
    {
        try {
            $supplier = MSupplier::findByCompositeKey($kodeDivisi, $kodeSupplier);
            
            if (!$supplier) {
                return response()->json([
                    'success' => false,
                    'message' => 'Supplier not found'
                ], 404);
            }

            $supplier->delete();

            return response()->json([
                'success' => true,
                'message' => 'Supplier deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete supplier',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getNamaSupplier(Request $request)
    {
        $kodedivisi = $request->input('kodedivisi');
        $noref = $request->input('noref');

        $nama = null;

        $partPenerimaan = PartPenerimaan::where('KodeDivisi', $kodedivisi)->where('NoPenerimaan', $noref)->first();
        if ($partPenerimaan) {
            $supplier = MSupplier::where('KodeDivisi', $kodedivisi)->where('KodeSupplier', $partPenerimaan->KodeSupplier)->first();
            if ($supplier) {
                $nama = $supplier->NamaSupplier;
            }
        }

        return response()->json(['nama' => $nama]);
    }
}
