<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $companies = Company::orderBy('id', 'asc')->get();
            
            return response()->json([
                'success' => true,
                'message' => 'Data companies retrieved successfully',
                'data' => $companies,
                'total_records' => $companies->count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve companies data',
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
                'namacompany' => 'required|string|max:100',
                'alamat' => 'nullable|string|max:255',
                'kota' => 'nullable|string|max:50',
                'telp' => 'nullable|string|max:20',
                'fax' => 'nullable|string|max:20',
                'email' => 'nullable|email|max:100',
                'website' => 'nullable|string|max:100',
                'npwp' => 'nullable|string|max:20',
                'direktur' => 'nullable|string|max:100'
            ]);

            $company = Company::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Company created successfully',
                'data' => $company
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create company',
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
            $company = Company::findOrFail($id);
            
            return response()->json([
                'success' => true,
                'message' => 'Company data retrieved successfully',
                'data' => $company
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Company not found',
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
            $company = Company::findOrFail($id);
            
            $request->validate([
                'namacompany' => 'required|string|max:100',
                'alamat' => 'nullable|string|max:255',
                'kota' => 'nullable|string|max:50',
                'telp' => 'nullable|string|max:20',
                'fax' => 'nullable|string|max:20',
                'email' => 'nullable|email|max:100',
                'website' => 'nullable|string|max:100',
                'npwp' => 'nullable|string|max:20',
                'direktur' => 'nullable|string|max:100'
            ]);

            $company->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Company updated successfully',
                'data' => $company
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update company',
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
            $company = Company::findOrFail($id);
            $company->delete();

            return response()->json([
                'success' => true,
                'message' => 'Company deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete company',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
