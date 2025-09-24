<?php

namespace Tests\Feature;

use Tests\TestCase;
use Tests\Traits\CreatesTestData;
use PHPUnit\Framework\Attributes\Test;

class KategoriApiTest extends TestCase
{
    use CreatesTestData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsTestUser();
    }

    #[Test]
    public function it_lists_categories_with_envelope(): void
    {
        $this->createTestKategori([
            'kode_kategori' => 'KATAPI',
            'kategori' => 'API Category',
            'status' => true,
        ]);

        $res = $this->getJson("/api/kategoris?per_page=5");
        $res->assertOk()
            ->assertJsonStructure([
                'data',
                'summary' => ['total_kategoris'],
                'meta' => ['total','active_count','inactive_count'],
                'pagination' => ['current_page','last_page','per_page','total'],
            ]);
    }

    #[Test]
    public function it_creates_category_with_normalization(): void
    {
        $payload = [
            'kode_kategori' => 'katx01',
            'kategori' => 'Created From Test',
            'status' => '1',
        ];

        $res = $this->postJson("/api/kategoris", $payload);
        $res->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.kode_kategori', 'KATX01')
            ->assertJsonPath('data.status', true);
    }

    #[Test]
    public function it_shows_category_and_updates_it(): void
    {
        $this->createTestKategori([
            'kode_kategori' => 'KATU01',
            'kategori' => 'Before Update',
            'status' => true,
        ]);

        $show = $this->getJson("/api/kategoris/KATU01");
        $show->assertOk()->assertJsonPath('data.kode_kategori', 'KATU01');

        $upd = $this->putJson("/api/kategoris/KATU01", [
            'kategori' => 'After Update',
        ]);
        $upd->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.kategori', 'After Update');
    }

    #[Test]
    public function it_deletes_category_when_unused(): void
    {
        $this->createTestKategori([
            'kode_kategori' => 'KATD01',
            'kategori' => 'Delete Me',
            'status' => true,
        ]);

        $del = $this->deleteJson("/api/kategoris/KATD01");
        $del->assertOk()->assertJsonPath('success', true);
    }

    #[Test]
    public function it_returns_stats(): void
    {
        $res = $this->getJson("/api/kategoris/stats");
        $res->assertOk()
            ->assertJsonStructure(['data' => ['total_kategoris','active_kategoris','inactive_kategoris']]);
    }
}