<?php

namespace App\Http\Controllers;

use App\Models\MCust;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        try {
            // Mapping field database ke format frontend
            $customers = MCust::select(
                'id',
                'kodecust as kode_customer',
                'namacust as nama',
                'alamat',
                'telepon',
                'email',
                'status'
            )->get();

            return response()->json([
                'success' => true,
                'data' => $customers,
                'message' => 'Data customer berhasil diambil dari dbo.m_cust'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $customer = MCust::create([
                'kodecust' => $request->kode_customer,
                'namacust' => $request->nama,
                'alamat' => $request->alamat,
                'telepon' => $request->telepon,
                'email' => $request->email,
                'status' => $request->status ?? 'aktif'
            ]);

            return response()->json([
                'success' => true,
                'data' => $customer,
                'message' => 'Customer berhasil ditambahkan'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $customer = MCust::find($id);
            
            if (!$customer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Customer tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $customer
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $customer = MCust::find($id);
            
            if (!$customer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Customer tidak ditemukan'
                ], 404);
            }

            $customer->update([
                'kodecust' => $request->kode_customer,
                'namacust' => $request->nama,
                'alamat' => $request->alamat,
                'telepon' => $request->telepon,
                'email' => $request->email,
                'status' => $request->status
            ]);

            return response()->json([
                'success' => true,
                'data' => $customer,
                'message' => 'Customer berhasil diupdate'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $customer = MCust::find($id);
            
            if (!$customer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Customer tidak ditemukan'
                ], 404);
            }

            $customer->delete();

            return response()->json([
                'success' => true,
                'message' => 'Customer berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}
