<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\Traits\CreatesTestData;
use App\Models\Barang;
use App\Models\User;

/**
 * Simple Barang API Test with better data isolation
 */
class BarangApiTestSimple extends TestCase
{
    use WithFaker, CreatesTestData;

    protected User $testUser;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create unique test user and get divisi
        $this->testUser = $this->createTestUser();
    }

    /**
     * Test basic API endpoint accessibility
     */
    public function test_api_endpoints_are_accessible(): void
    {
        $testDivisi = $this->testUser->kode_divisi;
        
        $response = $this->actingAs($this->testUser)
                         ->getJson("/api/divisi/{$testDivisi}/barangs");
        
        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data',
                    'summary',
                    'filters_applied',
                    'api_version',
                ]);
    }

    /**
     * Test unauthenticated requests are rejected
     */
    public function test_unauthenticated_requests_are_rejected(): void
    {
        $testDivisi = $this->testUser->kode_divisi;
        
        $response = $this->getJson("/api/divisi/{$testDivisi}/barangs");
        $response->assertStatus(401);
    }

    /**
     * Test creating a new barang
     */
    public function test_can_create_barang(): void
    {
        $testDivisi = $this->testUser->kode_divisi;
        
        $testBarang = [
            'kode_barang' => 'TST001',
            'nama_barang' => 'Test Product',
            'kode_kategori' => 'KAT001',
            'harga_list' => 100000,
            'harga_jual' => 90000,
            'status' => 1
        ];

        $response = $this->actingAs($this->testUser)
                         ->postJson("/api/divisi/{$testDivisi}/barangs", $testBarang);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'message',
                    'data' => [
                        'kode_divisi',
                        'kode_barang',
                        'nama_barang',
                        'pricing' => [
                            'harga_list',
                            'harga_jual'
                        ]
                    ]
                ]);
    }
}