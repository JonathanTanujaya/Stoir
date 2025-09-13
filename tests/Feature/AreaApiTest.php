<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\CreatesTestData;

class AreaApiTest extends TestCase
{
    use RefreshDatabase, CreatesTestData;

    protected string $kodeDivisi;

    protected function setUp(): void
    {
        parent::setUp();
        $this->kodeDivisi = $this->createTestDivisi()->kode_divisi;
        $this->actingAsTestUser();
    }

    public function test_it_lists_areas_with_pagination_and_meta(): void
    {
        $area1 = $this->createTestArea($this->kodeDivisi, ['kode_area' => 'AR' . substr(self::$testRunId, 0, 3)]);
        $area2 = $this->createTestArea($this->kodeDivisi, ['kode_area' => 'ARX' . substr(self::$testRunId, 0, 2)]);

        $res = $this->getJson("/api/divisi/{$this->kodeDivisi}/areas?per_page=5");

        $res->assertOk()->assertJsonStructure([
            'data', 'summary', 'pagination', 'meta'
        ]);

        $res->assertJsonPath('summary.total_areas', 2);
    }

    public function test_it_creates_area_with_normalization(): void
    {
        $payload = [
            'kode_area' => 'ar01',
            'area' => 'Nama Area',
            'status' => '1',
        ];

        $res = $this->postJson("/api/divisi/{$this->kodeDivisi}/areas", $payload);

        $res->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.kode_area', 'AR01')
            ->assertJsonPath('data.status', true);
    }

    public function test_it_shows_updates_and_deletes_area(): void
    {
        $area = $this->createTestArea($this->kodeDivisi);

        $show = $this->getJson("/api/divisi/{$this->kodeDivisi}/areas/{$area['kode_area']}");
        $show->assertOk()->assertJsonPath('data.kode_area', $area['kode_area']);

        $update = $this->putJson("/api/divisi/{$this->kodeDivisi}/areas/{$area['kode_area']}", [
            'area' => 'Area Diubah',
            'status' => false,
        ]);
        $update->assertOk()->assertJsonPath('data.area', 'Area Diubah')
            ->assertJsonPath('data.status', false)
            ->assertJsonPath('success', true);

        $delete = $this->deleteJson("/api/divisi/{$this->kodeDivisi}/areas/{$area['kode_area']}");
        $delete->assertOk()->assertJsonPath('success', true);
    }

    public function test_it_returns_area_stats(): void
    {
        $this->createTestArea($this->kodeDivisi, ['status' => true]);
        $this->createTestArea($this->kodeDivisi, ['kode_area' => 'AR' . substr(self::$testRunId, 1, 3), 'status' => false]);

        $res = $this->getJson("/api/divisi/{$this->kodeDivisi}/areas/stats");
        $res->assertOk()->assertJsonStructure([
            'success', 'data' => ['total_areas', 'active_areas', 'inactive_areas', 'total_customers', 'total_sales']
        ]);
    }
}
