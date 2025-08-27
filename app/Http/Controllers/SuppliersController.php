<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MSupplier;
use Illuminate\Support\Facades\Validator;

class SuppliersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $suppliers = MSupplier::all();
            
            // Transform data to match frontend expectations
            $transformedSuppliers = $suppliers->map(function ($supplier) {
                return [
                    'id' => $supplier->kodesupplier,
                    'nama' => $supplier->namasupplier,
                    'kodeSupplier' => $supplier->kodesupplier,
                    'alamat' => $supplier->alamat ?? '',
                    'telepon' => $supplier->telepon ?? '',
                    'email' => $supplier->email ?? '',
                    'kontakPerson' => $supplier->kontakperson ?? '',
                    'status' => (bool)$supplier->status
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $transformedSuppliers,
                'totalCount' => $transformedSuppliers->count(),
                'message' => 'Suppliers retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => 'Error retrieving suppliers: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'nama' => 'required|string|max:255',
                'kodeSupplier' => 'required|string|max:50|unique:dbo.m_supplier,kodesupplier',
                'alamat' => 'nullable|string',
                'telepon' => 'nullable|string|max:20',
                'email' => 'nullable|email|max:255',
                'kontak_person' => 'nullable|string|max:255',
                'status' => 'in:aktif,tidak aktif'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'data' => null,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $supplier = MSupplier::create([
                'kodesupplier' => $request->kodeSupplier,
                'namasupplier' => $request->nama,
                'alamat' => $request->alamat,
                'telepon' => $request->telepon,
                'email' => $request->email,
                'kontakperson' => $request->kontakPerson,
                'status' => $request->status === 'aktif'
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $supplier->kodesupplier,
                    'nama' => $supplier->namasupplier,
                    'kodeSupplier' => $supplier->kodesupplier,
                    'alamat' => $supplier->alamat,
                    'telepon' => $supplier->telepon,
                    'email' => $supplier->email,
                    'kontakPerson' => $supplier->kontakperson,
                    'status' => (bool)$supplier->status
                ],
                'message' => 'Supplier created successfully'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Error creating supplier: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $supplier = MSupplier::where('kodesupplier', $id)->first();

            if (!$supplier) {
                return response()->json([
                    'success' => false,
                    'data' => null,
                    'message' => 'Supplier not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $supplier->kodesupplier,
                    'nama' => $supplier->namasupplier,
                    'kode_supplier' => $supplier->kodesupplier,
                    'alamat' => $supplier->alamat,
                    'telepon' => $supplier->telepon,
                    'email' => $supplier->email,
                    'kontak_person' => $supplier->kontakperson,
                    'status' => $supplier->status ? 'aktif' : 'tidak aktif'
                ],
                'message' => 'Supplier retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Error retrieving supplier: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $supplier = MSupplier::where('kodesupplier', $id)->first();

            if (!$supplier) {
                return response()->json([
                    'success' => false,
                    'data' => null,
                    'message' => 'Supplier not found'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'nama' => 'required|string|max:255',
                'kodeSupplier' => 'required|string|max:50|unique:dbo.m_supplier,kodesupplier,' . $id . ',kodesupplier',
                'alamat' => 'nullable|string',
                'telepon' => 'nullable|string|max:20',
                'email' => 'nullable|email|max:255',
                'kontak_person' => 'nullable|string|max:255',
                'status' => 'in:aktif,tidak aktif'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'data' => null,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $supplier->update([
                'kodesupplier' => $request->kodeSupplier,
                'namasupplier' => $request->nama,
                'alamat' => $request->alamat,
                'telepon' => $request->telepon,
                'email' => $request->email,
                'kontakperson' => $request->kontakPerson,
                'status' => $request->status === 'aktif'
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $supplier->kodesupplier,
                    'nama' => $supplier->namasupplier,
                    'kodeSupplier' => $supplier->kodesupplier,
                    'alamat' => $supplier->alamat,
                    'telepon' => $supplier->telepon,
                    'email' => $supplier->email,
                    'kontakPerson' => $supplier->kontakperson,
                    'status' => (bool)$supplier->status
                ],
                'message' => 'Supplier updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Error updating supplier: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $supplier = MSupplier::where('kodesupplier', $id)->first();

            if (!$supplier) {
                return response()->json([
                    'success' => false,
                    'data' => null,
                    'message' => 'Supplier not found'
                ], 404);
            }

            $supplier->delete();

            return response()->json([
                'success' => true,
                'data' => null,
                'message' => 'Supplier deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Error deleting supplier: ' . $e->getMessage()
            ], 500);
        }
    }
}
