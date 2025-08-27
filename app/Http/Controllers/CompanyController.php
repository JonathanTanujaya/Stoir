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
            $companies = Company::orderBy('companyname', 'asc')->get();
            $data = $companies->map(fn($c)=>[
                'companyName' => $c->companyname,
                'alamat' => $c->alamat,
                'kota' => $c->kota,
                'an' => $c->an,
                'telp' => $c->telp,
                'npwp' => $c->npwp
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Data companies retrieved successfully',
                'data' => $data,
                'totalCount' => $data->count()
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
                'companyname' => 'required|string|max:100',
                'alamat' => 'nullable|string|max:255',
                'kota' => 'nullable|string|max:50',
                'an' => 'nullable|string|max:100',
                'telp' => 'nullable|string|max:20',
                'npwp' => 'nullable|string|max:20'
            ]);

            $company = Company::create($request->only(['companyname','alamat','kota','an','telp','npwp']));

            return response()->json([
                'success' => true,
                'message' => 'Company created successfully',
                'data' => [
                    'companyName' => $company->companyname,
                    'alamat' => $company->alamat,
                    'kota' => $company->kota,
                    'an' => $company->an,
                    'telp' => $company->telp,
                    'npwp' => $company->npwp
                ]
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
    public function show($companyname)
    {
        try {
            $company = Company::findOrFail($companyname);
            
            return response()->json([
                'success' => true,
                'message' => 'Company data retrieved successfully',
                'data' => [
                    'companyName' => $company->companyname,
                    'alamat' => $company->alamat,
                    'kota' => $company->kota,
                    'an' => $company->an,
                    'telp' => $company->telp,
                    'npwp' => $company->npwp
                ]
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
    public function update(Request $request, $companyname)
    {
        try {
            $company = Company::findOrFail($companyname);
            
            $request->validate([
                'alamat' => 'nullable|string|max:255',
                'kota' => 'nullable|string|max:50',
                'an' => 'nullable|string|max:100',
                'telp' => 'nullable|string|max:20',
                'npwp' => 'nullable|string|max:20'
            ]);

            $company->update($request->only(['alamat','kota','an','telp','npwp']));

            return response()->json([
                'success' => true,
                'message' => 'Company updated successfully',
                'data' => [
                    'companyName' => $company->companyname,
                    'alamat' => $company->alamat,
                    'kota' => $company->kota,
                    'an' => $company->an,
                    'telp' => $company->telp,
                    'npwp' => $company->npwp
                ]
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
    public function destroy($companyname)
    {
        try {
            $company = Company::findOrFail($companyname);
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
