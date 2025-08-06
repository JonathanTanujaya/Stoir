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
            'username' => 'required|string|max:255|unique:master_users',
            'email' => 'required|string|email|max:255|unique:master_users',
            'password' => 'required|string|min:8',
        ]);

        $masterUser = MasterUser::create($request->all());
        return response()->json($masterUser, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $masterUser = MasterUser::findOrFail($id);
        return response()->json($masterUser);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'username' => 'required|string|max:255|unique:master_users,username,' . $id,
            'email' => 'required|string|email|max:255|unique:master_users,email,' . $id,
            'password' => 'sometimes|string|min:8',
        ]);

        $masterUser = MasterUser::findOrFail($id);
        $masterUser->update($request->all());
        return response()->json($masterUser);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $masterUser = MasterUser::findOrFail($id);
        $masterUser->delete();
        return response()->json(null, 204);
    }
}
