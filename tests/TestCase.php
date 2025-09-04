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
        // Skip company creation for now since it's not in SQLite test database
        // Create test company
        // Company::create([
        //     'company_name' => 'Test Company',
        //     'alamat' => 'Test Address',
        // ]);

        // Create test division
        MDivisi::create([
            'kode_divisi' => 'TEST',
            'nama_divisi' => 'Test Division',
        ]);

        // Create test users
        MasterUser::create([
            'kode_divisi' => 'TEST',
            'username' => 'testadmin',
            'password' => 'password123',
            'nama' => 'Test Admin',
        ]);

        MasterUser::create([
            'kode_divisi' => 'TEST',
            'username' => 'testuser',
            'password' => 'password123',
            'nama' => 'Test User',
        ]);
    }

    /**
     * Create authenticated user for testing
     */
    protected function createAuthenticatedUser($level = 'User'): MasterUser
    {
        $user = MasterUser::create([
            'kode_divisi' => 'TEST',
            'username' => 'auth_test_' . uniqid(),
            'password' => 'password123',
            'nama' => 'Auth Test User',
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
