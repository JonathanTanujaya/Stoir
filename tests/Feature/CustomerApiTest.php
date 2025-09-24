<?php

namespace Tests\Feature;

use Tests\TestCase;
use Tests\Traits\CreatesTestData;
use PHPUnit\Framework\Attributes\Test;

class CustomerApiTest extends TestCase
{
    use CreatesTestData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsTestUser();
    }

    #[Test]
    public function it_lists_customers(): void
    {
        $area = $this->createTestArea();
        $this->createTestCustomer('CSTX1', [
            'kode_area' => $area['kode_area'],
        ]);

        $res = $this->getJson("/api/customers?per_page=5");
        $res->assertOk()->assertJsonStructure([
            'data', 'summary', 'pagination', 'meta'
        ]);
    }

    #[Test]
    public function it_creates_customer_with_normalization(): void
    {
        $area = $this->createTestArea();

        $payload = [
            'kode_cust' => 'cst01',
            'nama_cust' => 'Test Customer',
            'kode_area' => strtolower($area['kode_area']),
            'alamat' => 'Jl. Test',
            'status' => '1',
        ];

        $res = $this->postJson("/api/customers", $payload);
        $res->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.kode_cust', 'CST01')
            ->assertJsonPath('data.business_info.status', true);
    }

    #[Test]
    public function it_shows_updates_and_deletes_customer(): void
    {
        $area = $this->createTestArea();
        $this->createTestCustomer('CSU01', [
            'kode_area' => $area['kode_area'],
        ]);

    $show = $this->getJson("/api/customers/CSU01");
    $show->assertOk()->assertJsonPath('data.kode_cust', 'CSU01');

        $upd = $this->putJson("/api/customers/CSU01", [
            'nama_cust' => 'Updated Name',
            'status' => '0',
        ]);
        $upd->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.nama_cust', 'Updated Name')
            ->assertJsonPath('data.business_info.status', false);

    $del = $this->deleteJson("/api/customers/CSU01");
        $del->assertOk()->assertJsonPath('success', true);
    }
}