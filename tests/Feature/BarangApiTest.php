<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\Traits\CreatesTestData;
use App\Models\Barang;
use App\Models\User;

class BarangApiTest extends TestCase
{
    use WithFaker, CreatesTestData;

    protected string $testKategori = 'KAT001';
    protected User $testUser;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->testUser = $this->createTestUser();

        $this->createTestKategori([
            'kode_kategori' => $this->testKategori,
            'kategori' => 'Test Category'
        ]);
    }

    public function test_unauthenticated_requests_are_rejected(): void
    {
        $response = $this->getJson("/api/barangs");
        $response->assertStatus(401);

        $response = $this->postJson("/api/barangs", [
            'kode_barang' => 'TEST001',
            'nama_barang' => 'Test Product'
        ]);
        $response->assertStatus(401);

        $response = $this->putJson("/api/barangs/TEST001", [
            'nama_barang' => 'Updated Product'
        ]);
        $response->assertStatus(401);

        $response = $this->deleteJson("/api/barangs/TEST001");
        $response->assertStatus(401);
    }

    public function test_api_endpoints_are_accessible(): void
    {
        $response = $this->actingAs($this->testUser)
                         ->getJson("/api/barangs");
        
        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data',
                    'summary',
                    'filters_applied',
                    'api_version',
                    'timestamp'
                ]);
    }

    public function test_can_list_barangs_with_pagination(): void
    {
        $response = $this->actingAs($this->testUser)
                         ->getJson("/api/barangs?per_page=5");

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'kode_barang',
                            'nama_barang',
                            'pricing' => [
                                'harga_list',
                                'harga_jual',
                                'harga_list_raw',
                                'harga_jual_raw'
                            ],
                            'inventory' => [
                                'satuan',
                                'stok_min',
                                'status',
                                'status_text'
                            ],
                            'meta' => [
                                'created_at',
                                'updated_at'
                            ]
                        ]
                    ],
                    'summary',
                    'pagination'
                ]);
    }

    public function test_can_search_barangs(): void
    {
        $this->createTestBarang('LAPTOP001', [
            'nama_barang' => 'Gaming Laptop Pro',
            'merk' => 'TechBrand'
        ]);
        
        $this->createTestBarang('DESKTOP001', [
            'nama_barang' => 'Desktop Computer',
            'merk' => 'OtherBrand'
        ]);

        $response = $this->actingAs($this->testUser)
                         ->getJson("/api/barangs?search=laptop");

        $response->assertStatus(200);
        
        $data = $response->json('data');
        $this->assertIsArray($data);
        
        $this->assertCount(1, $data);
        $this->assertEquals('LAPTOP001', $data[0]['kode_barang']);
        
        $response->assertJson([
            'filters_applied' => [
                'search' => 'laptop'
            ]
        ]);
    }

    public function test_can_create_barang_with_valid_data(): void
    {
        $this->cleanupTestBarang('API001');
        
        $testBarang = [
            'kode_barang' => 'API001',
            'nama_barang' => 'API Test Product',
            'kode_kategori' => $this->testKategori,
            'harga_list' => 100000,
            'harga_jual' => 120000,
            'satuan' => 'PCS',
            'disc1' => 5,
            'disc2' => 2,
            'merk' => 'Test Brand',
            'barcode' => '12345678',
            'status' => true,
            'lokasi' => 'A1-01',
            'stok_min' => 10
        ];

        $response = $this->actingAs($this->testUser)
                         ->postJson("/api/barangs", $testBarang);

        $response->assertStatus(201)
                ->assertJson([
                    'success' => true,
                    'message' => 'Barang berhasil dibuat'
                ])
                ->assertJsonStructure([
                    'data' => [
                        'kode_barang',
                        'nama_barang',
                        'pricing',
                        'inventory',
                        'discounts',
                        'product_info',
                        'meta'
                    ]
                ]);

        $this->assertDatabaseHas('m_barang', [
            'kode_barang' => 'API001',
            'nama_barang' => 'API Test Product'
        ]);
    }

    public function test_validation_errors_when_creating_invalid_barang(): void
    {
        $invalidData = [
            'nama_barang' => '',
            'harga_list' => -100,
            'disc1' => 150,
        ];

        $response = $this->actingAs($this->testUser)
                         ->postJson("/api/barangs", $invalidData);

        $response->assertStatus(422)
                ->assertJsonValidationErrors([
                    'kode_barang',
                    'nama_barang', 
                    'kode_kategori',
                    'harga_list',
                    'disc1'
                ]);
    }

    public function test_unique_constraint_validation(): void
    {
        $testBarang = [
            'kode_barang' => 'UNIQUE001',
            'nama_barang' => 'Unique Test Product',
            'kode_kategori' => $this->testKategori
        ];

        $this->actingAs($this->testUser)
             ->postJson("/api/barangs", $testBarang);

        $duplicateBarang = [
            'kode_barang' => 'UNIQUE001',
            'nama_barang' => 'Another Product',
            'kode_kategori' => $this->testKategori
        ];

        $response = $this->actingAs($this->testUser)
                         ->postJson("/api/barangs", $duplicateBarang);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['kode_barang']);
    }

    public function test_can_show_specific_barang(): void
    {
        $barang = $this->createTestBarang('SHOW001');

        $response = $this->actingAs($this->testUser)
                         ->getJson("/api/barangs/SHOW001");

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'data' => [
                        'kode_barang' => 'SHOW001'
                    ]
                ])
                ->assertJsonStructure([
                    'data' => [
                        'kode_barang',
                        'nama_barang',
                        'pricing',
                        'inventory',
                        'discounts',
                        'product_info',
                        'meta',
                        'relationships'
                    ]
                ]);
    }

    public function test_404_when_showing_non_existent_barang(): void
    {
        $response = $this->actingAs($this->testUser)
                         ->getJson("/api/barangs/NONEXISTENT");

        $response->assertStatus(404)
                ->assertJson([
                    'success' => false,
                    'message' => 'Barang tidak ditemukan'
                ]);
    }

    public function test_can_update_barang(): void
    {
        $this->createTestBarang('UPDATE001');

        $updateData = [
            'nama_barang' => 'Updated Product Name',
            'harga_jual' => 150000,
            'merk' => 'Updated Brand'
        ];

        $response = $this->actingAs($this->testUser)
                         ->putJson("/api/barangs/UPDATE001", $updateData);

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Barang berhasil diupdate',
                    'data' => [
                        'nama_barang' => 'Updated Product Name',
                        'pricing' => [
                            'harga_jual_raw' => 150000.0
                        ],
                        'product_info' => [
                            'merk' => 'Updated Brand'
                        ]
                    ]
                ]);

        $this->assertDatabaseHas('m_barang', [
            'kode_barang' => 'UPDATE001',
            'nama_barang' => 'Updated Product Name',
            'harga_jual' => 150000,
            'merk' => 'Updated Brand'
        ]);
    }

    public function test_can_delete_barang(): void
    {
        $this->createTestBarang('DELETE001');

        $response = $this->actingAs($this->testUser)
                         ->deleteJson("/api/barangs/DELETE001");

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true
                ]);

        $this->assertDatabaseMissing('m_barang', [
            'kode_barang' => 'DELETE001'
        ]);
    }

    public function test_api_response_headers(): void
    {
        $response = $this->actingAs($this->testUser)
                         ->getJson("/api/barangs");

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/json');
        
        $response->assertJsonStructure([
            'data',
            'api_version',
            'timestamp'
        ]);
    }

    public function test_can_filter_barangs(): void
    {
        $response = $this->actingAs($this->testUser)
                         ->getJson("/api/barangs?kategori={$this->testKategori}&status=1");

        $response->assertStatus(200)
                ->assertJson([
                    'filters_applied' => [
                        'kategori' => $this->testKategori,
                        'status' => '1'
                    ]
                ]);
    }

    public function test_can_sort_barangs(): void
    {
        $response = $this->actingAs($this->testUser)
                         ->getJson("/api/barangs?sort=harga_jual&direction=desc");

        $response->assertStatus(200);
    }

    private function createTestBarang(string $kodeBarang, array $additionalData = []): array
    {
        $this->cleanupTestBarang($kodeBarang);
        
        $data = array_merge([
            'kode_barang' => $kodeBarang,
            'nama_barang' => "Test Product {$kodeBarang}",
            'kode_kategori' => $this->testKategori,
            'harga_list' => 100000,
            'harga_jual' => 120000,
            'satuan' => 'PCS',
            'status' => true
        ], $additionalData);

        $this->actingAs($this->testUser)
             ->postJson("/api/barangs", $data);
        
        return $data;
    }

}