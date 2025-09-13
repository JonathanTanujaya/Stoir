<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\CreatesTestData;

class SalesApiTest extends TestCase
{
    use RefreshDatabase, CreatesTestData;

    protected string $kodeDivisi;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsTestUser();
        $this->kodeDivisi = $this->createTestDivisi()->kode_divisi;
    }

    public function test_it_lists_sales_with_pagination_and_filters(): void
    {
        $area = $this->createTestArea($this->kodeDivisi);

        \DB::table('m_sales')->insert([
            'kode_divisi' => $this->kodeDivisi,
            'kode_sales' => 'SL' . substr(self::$testRunId, 0, 3),
            'nama_sales' => 'Sales A',
            'kode_area' => $area['kode_area'],
            'alamat' => 'Alamat A',
            'no_hp' => '08123',
            'target' => 1000000,
            'status' => true,
        ]);

        $res = $this->getJson("/api/divisi/{$this->kodeDivisi}/sales?per_page=5&search=Sales&area={$area['kode_area']}&status=1&sort=kode_sales&direction=asc");
        $res->assertOk()->assertJsonStructure([
            'data', 'summary', 'pagination', 'filters_applied', 'api_version', 'timestamp', 'query_time'
        ]);
    }

    public function test_it_creates_sales_with_normalization(): void
    {
        $area = $this->createTestArea($this->kodeDivisi);

        $payload = [
            'kode_sales' => 'sl001',
            'nama_sales' => 'Nama Sales',
            'kode_area' => strtolower($area['kode_area']),
            'alamat' => 'Alamat',
            'no_hp' => '08123',
            'target' => 500000,
            'status' => '1',
        ];

        $res = $this->postJson("/api/divisi/{$this->kodeDivisi}/sales", $payload);
        $res->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.kode_sales', 'SL001')
            ->assertJsonPath('data.area.kode_area', $area['kode_area'])
            ->assertJsonPath('data.status', true);
    }

    public function test_it_shows_updates_and_deletes_sales(): void
    {
        $area = $this->createTestArea($this->kodeDivisi);
        \DB::table('m_sales')->insert([
            'kode_divisi' => $this->kodeDivisi,
            'kode_sales' => 'SL' . substr(self::$testRunId, 1, 3),
            'nama_sales' => 'Sales B',
            'kode_area' => $area['kode_area'],
            'alamat' => 'Alamat B',
            'no_hp' => '08123',
            'target' => 250000,
            'status' => true,
        ]);

        $kodeSales = 'SL' . substr(self::$testRunId, 1, 3);

        $show = $this->getJson("/api/divisi/{$this->kodeDivisi}/sales/{$kodeSales}");
        $show->assertOk()->assertJsonPath('data.kode_sales', $kodeSales);

        $update = $this->putJson("/api/divisi/{$this->kodeDivisi}/sales/{$kodeSales}", [
            'nama_sales' => 'Sales B Updated',
            'status' => false,
        ]);
        $update->assertOk()->assertJsonPath('success', true)
            ->assertJsonPath('data.nama_sales', 'Sales B Updated')
            ->assertJsonPath('data.status', false);

        $delete = $this->deleteJson("/api/divisi/{$this->kodeDivisi}/sales/{$kodeSales}");
        $delete->assertOk()->assertJsonPath('success', true);
    }
}
