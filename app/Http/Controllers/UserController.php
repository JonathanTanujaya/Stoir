<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $query = User::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('username', 'ILIKE', "%{$search}%")
                  ->orWhere('nama', 'ILIKE', "%{$search}%");
            });
        }

        // Filter by specific fields
        if ($request->filled('username')) {
            $query->where('username', 'ILIKE', "%{$request->get('username')}%");
        }

        if ($request->filled('nama')) {
            $query->where('nama', 'ILIKE', "%{$request->get('nama')}%");
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'username');
        $sortOrder = $request->get('sort_order', 'asc');
        
        if (in_array($sortBy, ['username', 'nama'])) {
            $query->orderBy($sortBy, $sortOrder);
        }

        // Pagination
        $perPage = min($request->get('per_page', 15), 100);
        $users = $query->paginate($perPage);

        return response(new UserCollection($users));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request): Response
    {
        $validated = $request->validated();
        
        // Hash password before storing
        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        return response(new UserResource($user), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $username): Response
    {
        $user = User::where('username', $username)->firstOrFail();

        return response(new UserResource($user));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, string $username): Response
    {
        $user = User::where('username', $username)->firstOrFail();

        $validated = $request->validated();
        
        // Hash password if provided
        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);

        return response(new UserResource($user));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $username): Response
    {
        $user = User::where('username', $username)->firstOrFail();

        // Check if user has any related data that would prevent deletion
        // Add business logic here if needed
        
        $user->delete();

        return response(null, 204);
    }

    /**
     * Get users statistics.
     */
    public function stats(): Response
    {
        $stats = [
            'total_users' => User::count(),
            'users_by_month' => User::selectRaw('EXTRACT(MONTH FROM created_at) as month, COUNT(*) as count')
                ->groupBy('month')
                ->orderBy('month')
                ->get()
                ->pluck('count', 'month')
                ->toArray()
        ];

        return response($stats);
    }
}
