<?php

namespace Tests\Traits;

use App\Models\User;
use App\Models\Divisi;
use Illuminate\Support\Str;

trait CreatesTestData
{
    protected static $testRunId;
    protected static $testCounter = 0;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Clean up any existing test data first
        $this->cleanupTestData();
        
        $this->ensureTestRunInitialized();
    }

    /**
     * Ensure the test run ID and counter are initialized. Safe to call multiple times.
     */
    protected function ensureTestRunInitialized(): void
    {
        if (! static::$testRunId) {
            static::$testRunId = substr(Str::uuid()->toString(), 0, 6) . '_' . substr((string) microtime(true), -6);
        }
        if (! is_int(static::$testCounter) || static::$testCounter < 0) {
            static::$testCounter = 0;
        }
    }

    /**
     * Create a unique test user for this test run
     */
    protected function createTestUser(array $attributes = []): User
    {
        $this->ensureTestRunInitialized();
        static::$testCounter++;
        
        $uniqueId = static::$testRunId . '_' . static::$testCounter;
        
        // Ensure test divisi exists
        $divisi = $this->createTestDivisi();
        
        $defaults = [
            'kode_divisi' => $divisi->kode_divisi,
            'username' => 'test_' . $uniqueId,
            'nama' => 'Test User ' . $uniqueId,
            'password' => 'test123',
        ];

        $userData = array_merge($defaults, $attributes);
        
        // Use direct DB insert to avoid factory issues
        \DB::table('master_user')->insert($userData);
        
        return User::where('kode_divisi', $userData['kode_divisi'])
                  ->where('username', $userData['username'])
                  ->first();
    }

    /**
     * Create a unique test divisi for this test run
     */
    protected function createTestDivisi(array $attributes = []): Divisi
    {
        $this->ensureTestRunInitialized();
    // Last 2 alphanumeric chars to keep 'TST' + XX <= 5 chars (column limit)
    $alnum = preg_replace('/[^A-Za-z0-9]/', '', (string) static::$testRunId);
    $uniqueId = strtoupper(substr($alnum, -2) ?: '01');
        
        $defaults = [
            'kode_divisi' => 'TST' . $uniqueId, // Max 5 chars total
            'nama_divisi' => 'Test Divisi ' . static::$testRunId,
        ];

        $divisiData = array_merge($defaults, $attributes);
        
        // Check if already exists
        $existing = Divisi::where('kode_divisi', $divisiData['kode_divisi'])->first();
        if ($existing) {
            return $existing;
        }
        
        // Use direct DB insert to avoid factory issues
        \DB::table('m_divisi')->insert($divisiData);
        
        return Divisi::where('kode_divisi', $divisiData['kode_divisi'])->first();
    }

    /**
     * Create a test kategori for the given divisi
     */
    protected function createTestKategori(string $kodeDivisi, array $attributes = []): array 
    {
        $this->ensureTestRunInitialized();
        $uniqueId = static::$testRunId;
        
        $defaults = [
            'kode_divisi' => $kodeDivisi,
            'kode_kategori' => 'KAT' . substr($uniqueId, 0, 3),
            'kategori' => 'Test Category ' . $uniqueId,
        ];

        $kategoriData = array_merge($defaults, $attributes);
        
        // Check if already exists
        $existing = \DB::table('m_kategori')
                      ->where('kode_divisi', $kategoriData['kode_divisi'])
                      ->where('kode_kategori', $kategoriData['kode_kategori'])
                      ->first();
        if ($existing) {
            return (array) $existing;
        }
        
        // Use direct DB insert to avoid factory issues
        \DB::table('m_kategori')->insert($kategoriData);
        
        return $kategoriData;
    }

    /**
     * Clean up test data (used in setUp and tearDown)
     */
    protected function cleanupTestData(): void
    {
        try {
            // Clean up test barang first (has foreign keys)
            \DB::table('m_barang')
                ->where('kode_divisi', 'like', 'TST%')
                ->delete();
                
            // Clean up test customers before areas (FK to area)
            \DB::table('m_cust')
                ->where('kode_divisi', 'like', 'TST%')
                ->delete();

            // Clean up test kategori
            \DB::table('m_kategori')
                ->where('kode_divisi', 'like', 'TST%')
                ->delete();

            // Clean up test areas
            \DB::table('m_area')
                ->where('kode_divisi', 'like', 'TST%')
                ->delete();
            
            // Clean up any test users (broader pattern)
            \DB::table('master_user')
                ->where('username', 'like', 'test_%')
                ->delete();
            
            // Clean up test divisi (broader pattern)
            \DB::table('m_divisi')
                ->where('kode_divisi', 'like', 'TST%')
                ->delete();
                
            // Clean up tokens
            \DB::table('personal_access_tokens')
                ->where('tokenable_type', User::class)
                ->delete();
        } catch (\Exception $e) {
            // Ignore cleanup errors - might be empty tables
        }
    }

    /**
     * Clean up specific test barang by kode_barang pattern
     */
    protected function cleanupTestBarang(string $pattern = '%'): void
    {
        try {
            \DB::table('m_barang')
                ->where('kode_divisi', 'like', 'TST%')
                ->where('kode_barang', 'like', $pattern)
                ->delete();
        } catch (\Exception $e) {
            // Ignore cleanup errors
        }
    }

    /**
     * Clean up test data after each test
     */
    protected function tearDown(): void
    {
        $this->cleanupTestData();
        parent::tearDown();
    }

    /**
     * Create authenticated user for API testing without Sanctum tokens
     */
    protected function actingAsTestUser(array $attributes = []): User
    {
        $user = $this->createTestUser($attributes);
        
        // Use Laravel's actingAs for testing instead of Sanctum tokens
        $this->actingAs($user);
        
        return $user;
    }

    /**
     * Create a test area row
     *
     * @return array{kode_divisi:string,kode_area:string,area:string,status:bool}
     */
    protected function createTestArea(string $kodeDivisi, array $attributes = []): array
    {
        $this->ensureTestRunInitialized();
    $unique = substr(static::$testRunId, 0, 3);

        $defaults = [
            'kode_divisi' => $kodeDivisi,
            'kode_area' => strtoupper('AR' . $unique), // ensure <=5 chars and uppercase
            'area' => 'Test Area ' . $unique,
            'status' => true,
        ];

        $data = array_merge($defaults, $attributes);

        $exists = \DB::table('m_area')
            ->where('kode_divisi', $data['kode_divisi'])
            ->where('kode_area', $data['kode_area'])
            ->first();
        if ($exists) {
            return (array) $exists;
        }

        \DB::table('m_area')->insert($data);
        return $data;
    }

    /**
     * Create a test customer row
     *
     * @return array<string,mixed>
     */
    protected function createTestCustomer(string $kodeCust, array $attributes = []): array
    {
        $this->ensureTestRunInitialized();
        $kodeDivisi = $attributes['kode_divisi'] ?? $this->createTestDivisi()->kode_divisi;
        $area = $this->createTestArea($kodeDivisi, [
            'kode_area' => $attributes['kode_area'] ?? ('AR' . substr(static::$testRunId, 0, 3)),
        ]);

        $defaults = [
            'kode_divisi' => $kodeDivisi,
            'kode_cust' => $kodeCust,
            'nama_cust' => 'Test Customer ' . $kodeCust,
            'kode_area' => $area['kode_area'],
            'alamat' => 'Test Address',
            'telp' => '081234567890',
            'status' => true,
        ];

        $data = array_merge($defaults, $attributes);

        // Upsert-like: if exists, update; else insert
        $exists = \DB::table('m_cust')
            ->where('kode_divisi', $data['kode_divisi'])
            ->where('kode_cust', $data['kode_cust'])
            ->first();
        if ($exists) {
            \DB::table('m_cust')
                ->where('kode_divisi', $data['kode_divisi'])
                ->where('kode_cust', $data['kode_cust'])
                ->update($data);
        } else {
            \DB::table('m_cust')->insert($data);
        }

        return $data;
    }

    /**
     * Cleanup helper for customers by kode pattern
     */
    protected function cleanupTestCustomer(string $pattern = '%'): void
    {
        try {
            \DB::table('m_cust')
                ->where('kode_divisi', 'like', 'TST%')
                ->where('kode_cust', 'like', $pattern)
                ->delete();
        } catch (\Exception $e) {
            // ignore
        }
    }
}