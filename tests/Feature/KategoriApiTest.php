<?php

namespace Tests\Feature;

use Tests\TestCase;
use Tests\Traits\CreatesTestData;
use PHPUnit\Framework\Attributes\Test;

class KategoriApiTest extends TestCase
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
    public function it_lists_categories_with_envelope(): void
    {
        // seed one kategori
        $this->createTestKategori($this->kodeDivisi, [
            'kode_kategori' => 'KATAPI',
            'kategori' => 'API Category',
            'status' => true,
        ]);

        $res = $this->getJson("/api/divisi/{$this->kodeDivisi}/kategoris?per_page=5");
        $res->assertOk()
            ->assertJsonStructure([
                'data',
                'summary' => ['total_kategoris'],
                'meta' => ['total','active_count','inactive_count','divisi_breakdown'],
                'pagination' => ['current_page','last_page','per_page','total'],
            ]);
    }

    #[Test]
    public function it_creates_category_with_normalization(): void
    {
        $payload = [
            'kode_kategori' => 'katx01', // lower, expect uppercased
            'kategori' => 'Created From Test',
            'status' => '1', // string truthy
        ];

        $res = $this->postJson("/api/divisi/{$this->kodeDivisi}/kategoris", $payload);
        $res->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.kode_kategori', 'KATX01')
            ->assertJsonPath('data.status', true);
    }

    #[Test]
    public function it_shows_category_and_updates_it(): void
    {
        $this->createTestKategori($this->kodeDivisi, [
            'kode_kategori' => 'KATU01',
            'kategori' => 'Before Update',
            'status' => true,
        ]);

        $show = $this->getJson("/api/divisi/{$this->kodeDivisi}/kategoris/KATU01");
        $show->assertOk()->assertJsonPath('data.kode_kategori', 'KATU01');

        $upd = $this->putJson("/api/divisi/{$this->kodeDivisi}/kategoris/KATU01", [
            'kategori' => 'After Update',
        ]);
        $upd->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.kategori', 'After Update');
    }

    #[Test]
    public function it_deletes_category_when_unused(): void
    {
        $this->createTestKategori($this->kodeDivisi, [
            'kode_kategori' => 'KATD01',
            'kategori' => 'Delete Me',
            'status' => true,
        ]);

        $del = $this->deleteJson("/api/divisi/{$this->kodeDivisi}/kategoris/KATD01");
        $del->assertOk()->assertJsonPath('success', true);
    }

    #[Test]
    public function it_returns_stats(): void
    {
        $res = $this->getJson("/api/divisi/{$this->kodeDivisi}/kategoris/stats");
        $res->assertOk()
            ->assertJsonStructure(['data' => ['total_kategoris','active_kategoris','inactive_kategoris']]);
    }
}
