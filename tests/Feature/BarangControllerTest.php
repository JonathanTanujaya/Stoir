<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\Traits\CreatesTestData;
use App\Models\Barang;
use App\Models\User;
use PHPUnit\Framework\Attributes\Test;

class BarangControllerTest extends TestCase
{
    use WithFaker, CreatesTestData;

    protected User $user;
    protected string $testKategori = 'KAT001';

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = $this->createTestUser();

        $this->createTestKategori([
            'kode_kategori' => $this->testKategori,
            'kategori' => 'Test Category'
        ]);
    }

    #[Test]
    public function it_can_list_barangs()
    {
        $response = $this->actingAs($this->user)->getJson("/api/barangs");

        $response->assertStatus(200)
                ->assertJsonStructure(['data']);
    }

    #[Test]
    public function it_can_show_a_barang()
    {
        \DB::table('m_barang')->insert([
            'kode_barang' => 'SHOW001',
            'nama_barang' => 'Test Product for Show',
            'kode_kategori' => $this->testKategori,
            'harga_list' => 100000,
            'harga_jual' => 120000,
            'satuan' => 'PCS',
            'status' => 1
        ]);

        $response = $this->actingAs($this->user)->getJson("/api/barangs/SHOW001");

        $response->assertStatus(200)
                ->assertJson(['data' => ['kode_barang' => 'SHOW001']]);
    }

    #[Test]
    public function it_can_create_a_barang()
    {
        $barangData = [
            'kode_barang' => 'CREATE001',
            'nama_barang' => 'Test Create Product',
            'kode_kategori' => $this->testKategori,
            'harga_list' => 100000,
            'harga_jual' => 120000,
            'satuan' => 'PCS',
            'status' => 1
        ];

        $response = $this->actingAs($this->user)->postJson("/api/barangs", $barangData);

        $response->assertStatus(201)
                ->assertJson(['data' => ['kode_barang' => 'CREATE001']]);
    }

    #[Test]
    public function it_validates_required_fields_when_creating_barang()
    {
        $response = $this->actingAs($this->user)->postJson("/api/barangs", []);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['kode_barang', 'nama_barang', 'kode_kategori']);
    }

    #[Test]
    public function it_validates_unique_kode_barang()
    {
        \DB::table('m_barang')->insert([
            'kode_barang' => 'UNIQUE001',
            'nama_barang' => 'First Product',
            'kode_kategori' => $this->testKategori,
            'harga_list' => 100000,
            'harga_jual' => 120000,
            'satuan' => 'PCS',
            'status' => 1
        ]);
        
        $barangData = [
            'kode_barang' => 'UNIQUE001', // Same kode_barang
            'nama_barang' => 'Another Test Barang',
            'kode_kategori' => $this->testKategori
        ];

        $response = $this->actingAs($this->user)->postJson("/api/barangs", $barangData);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['kode_barang']);
    }

    #[Test]
    public function it_can_search_barangs()
    {
        \DB::table('m_barang')->insert([
            'kode_barang' => 'SEARCH001',
            'nama_barang' => 'Searchable Product',
            'kode_kategori' => $this->testKategori,
            'harga_list' => 100000,
            'harga_jual' => 120000,
            'satuan' => 'PCS',
            'status' => 1
        ]);

        $response = $this->actingAs($this->user)->getJson("/api/barangs?search=Searchable");

        $response->assertStatus(200);
        $response->assertJsonStructure(['data']);
    }

    #[Test]
    public function it_can_get_categories()
    {
        $response = $this->actingAs($this->user)->getJson("/api/categories");

        $response->assertStatus(200)
                ->assertJsonStructure(['data'])
                ->assertJson(['data' => [['kode_kategori' => $this->testKategori]]]);
    }

    #[Test]
    public function it_can_update_a_barang()
    {
        \DB::table('m_barang')->insert([
            'kode_barang' => 'UPDATE001',
            'nama_barang' => 'Original Product Name',
            'kode_kategori' => $this->testKategori,
            'harga_list' => 100000,
            'harga_jual' => 120000,
            'satuan' => 'PCS',
            'status' => 1
        ]);
        
        $updateData = [
            'nama_barang' => 'Updated Test Barang Name',
        ];

        $response = $this->actingAs($this->user)->putJson("/api/barangs/UPDATE001", $updateData);

        $response->assertStatus(200)
                ->assertJson(['data' => ['nama_barang' => 'Updated Test Barang Name']]);
    }

    #[Test]
    public function it_returns_404_for_non_existent_barang()
    {
        $response = $this->actingAs($this->user, 'sanctum')->getJson('/api/barangs/NONEXISTENT');

        $response->assertStatus(404);
    }
}