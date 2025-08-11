<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MCust;
use Illuminate\Support\Facades\Validator;

class CustomersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $customers = MCust::all();
            
            // Transform data to match frontend expectations
            $transformedCustomers = $customers->map(function ($customer) {
                return [
                    'id' => $customer->kodecust,
                    'nama' => $customer->namacust,
                    'kode_customer' => $customer->kodecust,
                    'alamat' => $customer->alamat,
                    'telepon' => $customer->telepon,
                    'email' => $customer->email ?? '',
                    'status' => $customer->status ? 'aktif' : 'tidak aktif'
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $transformedCustomers,
                'message' => 'Customers retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => 'Error retrieving customers: ' . $e->getMessage()
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
                'kode_customer' => 'required|string|max:50|unique:dbo.m_cust,kodecust',
                'alamat' => 'required|string',
                'telepon' => 'required|string|max:20',
                'email' => 'nullable|email|max:255',
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

            $customer = MCust::create([
                'kodecust' => $request->kode_customer,
                'namacust' => $request->nama,
                'alamat' => $request->alamat,
                'telepon' => $request->telepon,
                'email' => $request->email,
                'status' => $request->status === 'aktif'
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $customer->kodecust,
                    'nama' => $customer->namacust,
                    'kode_customer' => $customer->kodecust,
                    'alamat' => $customer->alamat,
                    'telepon' => $customer->telepon,
                    'email' => $customer->email,
                    'status' => $customer->status ? 'aktif' : 'tidak aktif'
                ],
                'message' => 'Customer created successfully'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Error creating customer: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $customer = MCust::where('kodecust', $id)->first();

            if (!$customer) {
                return response()->json([
                    'success' => false,
                    'data' => null,
                    'message' => 'Customer not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $customer->kodecust,
                    'nama' => $customer->namacust,
                    'kode_customer' => $customer->kodecust,
                    'alamat' => $customer->alamat,
                    'telepon' => $customer->telepon,
                    'email' => $customer->email,
                    'status' => $customer->status ? 'aktif' : 'tidak aktif'
                ],
                'message' => 'Customer retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Error retrieving customer: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $customer = MCust::where('kodecust', $id)->first();

            if (!$customer) {
                return response()->json([
                    'success' => false,
                    'data' => null,
                    'message' => 'Customer not found'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'nama' => 'required|string|max:255',
                'kode_customer' => 'required|string|max:50|unique:dbo.m_cust,kodecust,' . $id . ',kodecust',
                'alamat' => 'required|string',
                'telepon' => 'required|string|max:20',
                'email' => 'nullable|email|max:255',
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

            $customer->update([
                'kodecust' => $request->kode_customer,
                'namacust' => $request->nama,
                'alamat' => $request->alamat,
                'telepon' => $request->telepon,
                'email' => $request->email,
                'status' => $request->status === 'aktif'
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $customer->kodecust,
                    'nama' => $customer->namacust,
                    'kode_customer' => $customer->kodecust,
                    'alamat' => $customer->alamat,
                    'telepon' => $customer->telepon,
                    'email' => $customer->email,
                    'status' => $customer->status ? 'aktif' : 'tidak aktif'
                ],
                'message' => 'Customer updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Error updating customer: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $customer = MCust::where('kodecust', $id)->first();

            if (!$customer) {
                return response()->json([
                    'success' => false,
                    'data' => null,
                    'message' => 'Customer not found'
                ], 404);
            }

            $customer->delete();

            return response()->json([
                'success' => true,
                'data' => null,
                'message' => 'Customer deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Error deleting customer: ' . $e->getMessage()
            ], 500);
        }
    }
}
