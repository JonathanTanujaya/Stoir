<?php

namespace Tests\Traits;

use App\Models\Kategori;
use App\Models\Barang;
use App\Models\Area;
use App\Models\Customer;
use App\Models\Sales;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

trait CreatesTestData
{
    protected static $testRunId;
    protected static $createdUsers = [];

    public static function bootCreatesTestData()
    {
        self::$testRunId = uniqid();
    }

    protected function createTestUser(array $attributes = []): User
    {
        $email = 'testuser' . self::$testRunId . '@example.com';
        if (isset(self::$createdUsers[$email])) {
            return self::$createdUsers[$email];
        }

        $user = User::factory()->create(array_merge([
            'name' => 'Test User ' . self::$testRunId,
            'email' => $email,
            'password' => Hash::make('password'),
            'role' => 'user',
        ], $attributes));

        self::$createdUsers[$email] = $user;
        return $user;
    }

    protected function createTestKategori(array $attributes = []): Kategori
    {
        $this->ensureTestRunInitialized();
        $uniqueId = static::$testRunId;
        
        $defaults = [
            'kode_kategori' => 'KAT' . substr($uniqueId, 0, 3),
            'kategori' => 'Test Category ' . $uniqueId,
        ];

        $kategoriData = array_merge($defaults, $attributes);
        
        $existing = \DB::table('m_kategori')
                      ->where('kode_kategori', $kategoriData['kode_kategori'])
                      ->first();
        if ($existing) {
            return (array) $existing;
        }
        
        \DB::table('m_kategori')->insert($kategoriData);
        
        return $kategoriData;
    }

    /**
     * Clean up test data (used in setUp and tearDown)
     */
    protected function cleanupTestData(): void
    {
        try {
            \DB::table('m_barang')
                ->where('kode_barang', 'like', 'TST%')
                ->delete();
                
            \DB::table('m_cust')
                ->where('kode_cust', 'like', 'TST%')
                ->delete();

            \DB::table('m_kategori')
                ->where('kode_kategori', 'like', 'TST%')
                ->delete();

            \DB::table('m_area')
                ->where('kode_area', 'like', 'TST%')
                ->delete();
            
            \DB::table('master_user')
                ->where('username', 'like', 'test_%')
                ->delete();
            
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
        
        $this->actingAs($user);
        
        return $user;
    }

    /**
     * Create a test area row
     *
     * @return array{kode_area:string,area:string,status:bool}
     */
    protected function createTestArea(array $attributes = []): array
    {
        $this->ensureTestRunInitialized();
    $unique = substr(static::$testRunId, 0, 3);

        $defaults = [
            'kode_area' => strtoupper('AR' . $unique), // ensure <=5 chars and uppercase
            'area' => 'Test Area ' . $unique,
            'status' => true,
        ];

        $data = array_merge($defaults, $attributes);

        $exists = \DB::table('m_area')
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
        $area = $this->createTestArea([
            'kode_area' => $attributes['kode_area'] ?? ('AR' . substr(static::$testRunId, 0, 3)),
        ]);

        $defaults = [
            'kode_cust' => $kodeCust,
            'nama_cust' => 'Test Customer ' . $kodeCust,
            'kode_area' => $area['kode_area'],
            'alamat' => 'Test Address',
            'telp' => '081234567890',
            'status' => true,
        ];

        $data = array_merge($defaults, $attributes);

        $exists = \DB::table('m_cust')
            ->where('kode_cust', $data['kode_cust'])
            ->first();
        if ($exists) {
            \DB::table('m_cust')
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
                ->where('kode_cust', 'like', $pattern)
                ->delete();
        } catch (\Exception $e) {
            // ignore
        }
    }
}
