<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Supplier;
use App\Models\Divisi;
use Laravel\Sanctum\Sanctum;

class SupplierApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected string $divisi = 'TEST';

    protected function setUp(): void
    {
        parent::setUp();

        // Ensure divisi exists
        Divisi::firstOrCreate(
            ['kode_divisi' => $this->divisi],
            ['nama_divisi' => 'Test Division']
        );

        // Authenticate a user in the same divisi
        $user = User::factory()->create([
            'kode_divisi' => $this->divisi,
            'username' => 'tester',
            'nama' => 'Test User',
        ]);
        Sanctum::actingAs($user);
    }

    public function test_index_lists_suppliers(): void
    {
        Supplier::factory()->count(3)->forDivision($this->divisi)->create();

        $res = $this->getJson("/api/divisi/{$this->divisi}/suppliers");
        $res->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'kode_divisi',
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

        $res = $this->postJson("/api/divisi/{$this->divisi}/suppliers", $payload);
        $res->assertStatus(201)
            ->assertJsonPath('data.kode_supplier', 'SUP001')
            ->assertJsonPath('data.nama_supplier', 'PT Satu');

        $this->assertDatabaseHas('m_supplier', [
            'kode_divisi' => $this->divisi,
            'kode_supplier' => 'SUP001',
        ]);
    }

    public function test_show_returns_single_supplier(): void
    {
        Supplier::factory()->forDivision($this->divisi)->withCode('SUP002')->create();

        $res = $this->getJson("/api/divisi/{$this->divisi}/suppliers/SUP002");
        $res->assertOk()->assertJsonPath('data.kode_supplier', 'SUP002');
    }

    public function test_update_modifies_supplier(): void
    {
        Supplier::factory()->forDivision($this->divisi)->withCode('SUP003')->create();

        $res = $this->putJson("/api/divisi/{$this->divisi}/suppliers/SUP003", [
            'nama_supplier' => 'Baru',
            'status' => 'N',
        ]);

        $res->assertOk()->assertJsonPath('message', 'Supplier berhasil diperbarui');
        $this->assertDatabaseHas('m_supplier', [
            'kode_divisi' => $this->divisi,
            'kode_supplier' => 'SUP003',
            'nama_supplier' => 'Baru',
            'status' => false,
        ]);
    }

    public function test_destroy_deletes_supplier(): void
    {
        Supplier::factory()->forDivision($this->divisi)->withCode('SUP004')->create();

        $res = $this->deleteJson("/api/divisi/{$this->divisi}/suppliers/SUP004");
        $res->assertOk()->assertJsonPath('message', 'Supplier berhasil dihapus');

        $this->assertDatabaseMissing('m_supplier', [
            'kode_divisi' => $this->divisi,
            'kode_supplier' => 'SUP004',
        ]);
    }
}
