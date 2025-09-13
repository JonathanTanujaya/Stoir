<?php

namespace Tests\Feature;

use Tests\TestCase;
use Tests\Traits\CreatesTestData;
use PHPUnit\Framework\Attributes\Test;

class CustomerApiTest extends TestCase
{
    use CreatesTestData;

    protected string $kodeDivisi;

    protected function setUp(): void
    {
        parent::setUp();
        $user = $this->actingAsTestUser();
        $this->kodeDivisi = $user->kode_divisi;
    }

    #[Test]
    public function it_lists_customers(): void
    {
        // seed area and a customer
        $area = $this->createTestArea($this->kodeDivisi);
        $this->createTestCustomer('CSTX1', [
            'kode_divisi' => $this->kodeDivisi,
            'kode_area' => $area['kode_area'],
        ]);

        $res = $this->getJson("/api/divisi/{$this->kodeDivisi}/customers?per_page=5");
        $res->assertOk()->assertJsonStructure([
            'data', 'summary', 'pagination', 'meta'
        ]);
    }

    #[Test]
    public function it_creates_customer_with_normalization(): void
    {
        $area = $this->createTestArea($this->kodeDivisi);

        $payload = [
            'kode_cust' => 'cst01', // lower will be uppercased (5 chars)
            'nama_cust' => 'Test Customer',
            'kode_area' => strtolower($area['kode_area']), // already <= 5
            'alamat' => 'Jl. Test',
            'status' => '1',
        ];

        $res = $this->postJson("/api/divisi/{$this->kodeDivisi}/customers", $payload);
        $res->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.kode_cust', 'CST01')
            ->assertJsonPath('data.business_info.status', true);
    }

    #[Test]
    public function it_shows_updates_and_deletes_customer(): void
    {
        $area = $this->createTestArea($this->kodeDivisi);
        $this->createTestCustomer('CSU01', [
            'kode_divisi' => $this->kodeDivisi,
            'kode_area' => $area['kode_area'],
        ]);

    $show = $this->getJson("/api/divisi/{$this->kodeDivisi}/customers/CSU01");
    $show->assertOk()->assertJsonPath('data.kode_cust', 'CSU01');

        $upd = $this->putJson("/api/divisi/{$this->kodeDivisi}/customers/CSU01", [
            'nama_cust' => 'Updated Name',
            'status' => '0',
        ]);
        $upd->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.nama_cust', 'Updated Name')
            ->assertJsonPath('data.business_info.status', false);

    $del = $this->deleteJson("/api/divisi/{$this->kodeDivisi}/customers/CSU01");
        $del->assertOk()->assertJsonPath('success', true);
    }
}
