<?php

namespace App\Http\Controllers;

use App\Models\MasterUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;

class AuthController extends Controller
{
    /**
     * Login user
     */
    public function login(LoginRequest $request)
    {
        try {
            \Log::info('Login attempt', [
                'username' => $request->username,
                'kodedivisi' => $request->kodedivisi
            ]);
            
            $user = MasterUser::findForAuth(
                $request->username, 
                $request->kodedivisi
            );

            \Log::info('User found', ['user' => $user ? $user->toArray() : null]);

            if (!$user || !$user->verifyPassword($request->password)) {
                \Log::info('Authentication failed');
                throw ValidationException::withMessages([
                    'credentials' => ['The provided credentials are incorrect.'],
                ]);
            }

            // Create token
            \Log::info('Creating token');
            $token = $user->createToken('api-token', ['*'])->plainTextToken;
            \Log::info('Token created successfully');

            return response()->json([
                'status' => 'success',
                'message' => 'Login successful',
                'data' => [
                    'user' => [
                        'kodedivisi' => $user->kodedivisi,
                        'username' => $user->username,
                        'nama' => $user->nama,
                        'is_admin' => $user->isAdmin(),
                        'accessible_modules' => $user->getAccessibleModules(),
                    ],
                    'token' => $token,
                ]
            ], 200);

        } catch (ValidationException $e) {
            \Log::error('Validation exception', ['errors' => $e->errors()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Authentication failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Login exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred',
                'errors' => ['system' => [$e->getMessage()]]
            ], 500);
        }
    }

    /**
     * Register new user (admin only)
     */
    public function register(RegisterRequest $request)
    {
        try {
            // Check if user already exists
            $existingUser = MasterUser::findByCompositeKey(
                $request->kodedivisi, 
                $request->username
            );

            if ($existingUser) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User already exists',
                    'errors' => ['user' => ['User with this division and username already exists']]
                ], 422);
            }

            $user = MasterUser::create([
                'kodedivisi' => $request->kodedivisi,
                'username' => $request->username,
                'nama' => $request->nama,
                'password' => $request->password, // Will be hashed by mutator
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'User registered successfully',
                'data' => [
                    'user' => [
                        'kodedivisi' => $user->kodedivisi,
                        'username' => $user->username,
                        'nama' => $user->nama,
                    ]
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Registration failed',
                'errors' => ['system' => [$e->getMessage()]]
            ], 500);
        }
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Logged out successfully'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Logout failed',
                'errors' => ['system' => [$e->getMessage()]]
            ], 500);
        }
    }

    /**
     * Get current user info
     */
    public function me(Request $request)
    {
        try {
            $user = $request->user();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'user' => [
                        'kodedivisi' => $user->kodedivisi,
                        'username' => $user->username,
                        'nama' => $user->nama,
                        'is_admin' => $user->isAdmin(),
                        'accessible_modules' => $user->getAccessibleModules(),
                        'divisi' => $user->divisi,
                    ]
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to get user info',
                'errors' => ['system' => [$e->getMessage()]]
            ], 500);
        }
    }

    /**
     * Change password
     */
    public function changePassword(Request $request)
    {
        try {
            $request->validate([
                'current_password' => 'required|string',
                'new_password' => 'required|string|min:6|confirmed',
            ]);

            $user = $request->user();

            if (!$user->verifyPassword($request->current_password)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Current password is incorrect',
                    'errors' => ['current_password' => ['Current password is incorrect']]
                ], 422);
            }

            $user->update([
                'password' => $request->new_password
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Password changed successfully'
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Password change failed',
                'errors' => ['system' => [$e->getMessage()]]
            ], 500);
        }
    }
}
