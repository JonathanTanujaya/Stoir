<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Supplier;
use Laravel\Sanctum\Sanctum;

class SupplierApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create([
            'username' => 'tester',
            'nama' => 'Test User',
        ]);
        Sanctum::actingAs($user);
    }

    public function test_index_lists_suppliers(): void
    {
        Supplier::factory()->count(3)->create();

        $res = $this->getJson("/api/suppliers");
        $res->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'kode_supplier',
                        'nama_supplier',
                        'contact_info' => ['alamat','telp','contact'],
                        'supplier_details' => ['status','status_text','full_address'],
                    ],
                ],
                'summary' => ['total_suppliers'],
                'pagination' => ['current_page','last_page','per_page','total'],
            ]);
    }

    public function test_store_creates_supplier(): void
    {
        $payload = [
            'kode_supplier' => 'SUP001',
            'nama_supplier' => 'PT Satu',
            'alamat' => 'Jl. Test 1',
            'telp' => '021-123456',
            'contact' => 'Budi',
            'status' => 'A',
        ];

        $res = $this->postJson("/api/suppliers", $payload);
        $res->assertStatus(201)
            ->assertJsonPath('data.kode_supplier', 'SUP001')
            ->assertJsonPath('data.nama_supplier', 'PT Satu');

        $this->assertDatabaseHas('m_supplier', [
            'kode_supplier' => 'SUP001',
        ]);
    }

    public function test_show_returns_single_supplier(): void
    {
        Supplier::factory()->withCode('SUP002')->create();

        $res = $this->getJson("/api/suppliers/SUP002");
        $res->assertOk()->assertJsonPath('data.kode_supplier', 'SUP002');
    }

    public function test_update_modifies_supplier(): void
    {
        Supplier::factory()->withCode('SUP003')->create();

        $res = $this->putJson("/api/suppliers/SUP003", [
            'nama_supplier' => 'Baru',
            'status' => 'N',
        ]);

        $res->assertOk()->assertJsonPath('message', 'Supplier berhasil diperbarui');
        $this->assertDatabaseHas('m_supplier', [
            'kode_supplier' => 'SUP003',
            'nama_supplier' => 'Baru',
            'status' => false,
        ]);
    }

    public function test_destroy_deletes_supplier(): void
    {
        Supplier::factory()->withCode('SUP004')->create();

        $res = $this->deleteJson("/api/suppliers/SUP004");
        $res->assertOk()->assertJsonPath('message', 'Supplier berhasil dihapus');

        $this->assertDatabaseMissing('m_supplier', [
            'kode_supplier' => 'SUP004',
        ]);
    }
}