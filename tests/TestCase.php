<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use App\Models\MasterUser;
use App\Models\MDivisi;
use App\Models\Company;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Seed basic test data
        $this->seedTestData();
    }

    /**
     * Seed basic test data
     */
    protected function seedTestData(): void
    {
        // Create test company
        Company::create([
            'kodecompany' => 'TEST01',
            'namacompany' => 'Test Company',
            'alamat' => 'Test Address',
            'status' => 'Active'
        ]);

        // Create test division
        MDivisi::create([
            'kodedivisi' => 'TEST',
            'namadivisi' => 'Test Division',
            'kodecompany' => 'TEST01',
            'status' => 'Active'
        ]);

        // Create test users
        MasterUser::create([
            'username' => 'testadmin',
            'password' => 'password123',
            'nama' => 'Test Admin',
            'email' => 'admin@test.com',
            'level' => 'Administrator',
            'status' => 'Active',
            'kodedivisi' => 'TEST'
        ]);

        MasterUser::create([
            'username' => 'testuser',
            'password' => 'password123',
            'nama' => 'Test User',
            'email' => 'user@test.com',
            'level' => 'User',
            'status' => 'Active',
            'kodedivisi' => 'TEST'
        ]);
    }

    /**
     * Create authenticated user for testing
     */
    protected function createAuthenticatedUser($level = 'User'): MasterUser
    {
        $user = MasterUser::create([
            'username' => 'auth_test_' . uniqid(),
            'password' => 'password123',
            'nama' => 'Auth Test User',
            'email' => 'authtest@test.com',
            'level' => $level,
            'status' => 'Active',
            'kodedivisi' => 'TEST'
        ]);

        return $user;
    }

    /**
     * Authenticate as user for API testing
     */
    protected function authenticateAs($user = null)
    {
        if (!$user) {
            $user = $this->createAuthenticatedUser();
        }

        $token = $user->createToken('test-token')->plainTextToken;
        
        return $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ]);
    }

    /**
     * Create test data with factory
     */
    protected function createTestData($model, $count = 1, $attributes = [])
    {
        return $model::factory($count)->create($attributes);
    }
}
