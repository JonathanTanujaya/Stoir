<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MasterUser; // Asumsi model MasterUser ada

class MasterUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $masterUsers = MasterUser::all();
        return response()->json($masterUsers);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'kodedivisi' => 'required|string|max:4',
            'username' => 'required|string|max:50',
            'nama' => 'required|string|max:100',
            'password' => 'required|string|min:4',
        ]);

        // Check if combination already exists
        $exists = MasterUser::where('kodedivisi', $request->kodedivisi)
                          ->where('username', $request->username)
                          ->exists();
        
        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'User with this kodedivisi and username combination already exists'
            ], 422);
        }

        $masterUser = MasterUser::create($request->all());
        return response()->json([
            'success' => true,
            'data' => $masterUser,
            'message' => 'Master user created successfully'
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $kodedivisi)
    {
        $masterUser = MasterUser::where('kodedivisi', $kodedivisi)->first();
        
        if (!$masterUser) {
            return response()->json([
                'success' => false,
                'message' => 'Master user not found'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'data' => $masterUser
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $kodedivisi)
    {
        $request->validate([
            'username' => 'required|string|max:50',
            'nama' => 'required|string|max:100',
            'password' => 'sometimes|string|min:4',
        ]);

        $masterUser = MasterUser::where('kodedivisi', $kodedivisi)->first();
        
        if (!$masterUser) {
            return response()->json([
                'success' => false,
                'message' => 'Master user not found'
            ], 404);
        }
        
        $masterUser->update($request->all());
        return response()->json([
            'success' => true,
            'data' => $masterUser,
            'message' => 'Master user updated successfully'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $kodedivisi)
    {
        $masterUser = MasterUser::where('kodedivisi', $kodedivisi)->first();
        
        if (!$masterUser) {
            return response()->json([
                'success' => false,
                'message' => 'Master user not found'
            ], 404);
        }
        
        $masterUser->delete();
        return response()->json([
            'success' => true,
            'message' => 'Master user deleted successfully'
        ]);
    }
}
