<?php

namespace Tests\Feature;

use Tests\TestCase;
use Tests\Traits\CreatesTestData;
use Illuminate\Foundation\Testing\WithFaker;

class InvoiceApiTest extends TestCase
{
    use WithFaker, CreatesTestData;

    protected string $kodeCust;
    protected string $kodeSales;

    protected function setUp(): void
    {
        parent::setUp();
        $user = $this->createTestUser();
        $this->actingAs($user);
        $this->kodeCust = 'C' . strtoupper(substr(self::$testRunId, 0, 4));
        $this->kodeSales = 'S' . strtoupper(substr(self::$testRunId, 0, 4));

        // seed required related rows (area, customer, sales)
        $divisi = $user->kode_divisi;
        $area = $this->createTestArea($divisi);
        // customer (upsert-like)
        $existsCust = \DB::table('m_cust')->where('kode_divisi', $divisi)->where('kode_cust', $this->kodeCust)->first();
        $custData = [
            'kode_divisi' => $divisi,
            'kode_cust' => $this->kodeCust,
            'nama_cust' => 'INV Customer',
            'kode_area' => $area['kode_area'],
            'alamat' => 'Jl. Test',
            'telp' => '0811111111',
            'status' => true,
        ];
        if ($existsCust) {
            \DB::table('m_cust')->where('kode_divisi', $divisi)->where('kode_cust', $this->kodeCust)->update($custData);
        } else {
            \DB::table('m_cust')->insert($custData);
        }
        // sales
        $existsSales = \DB::table('m_sales')->where('kode_divisi', $divisi)->where('kode_sales', $this->kodeSales)->first();
        $salesData = [
            'kode_divisi' => $divisi,
            'kode_sales' => $this->kodeSales,
            'nama_sales' => 'INV Sales',
            'alamat' => 'Jl. Sales',
            'status' => true,
        ];
        if ($existsSales) {
            \DB::table('m_sales')->where('kode_divisi', $divisi)->where('kode_sales', $this->kodeSales)->update($salesData);
        } else {
            \DB::table('m_sales')->insert($salesData);
        }
    }

    public function test_list_invoices_with_filters_and_pagination(): void
    {
        $user = auth()->user();
        $divisi = $user->kode_divisi;

        // create a few invoices via direct insert (minimal fields)
        foreach (range(1, 3) as $i) {
            \DB::table('invoice')->insert([
                'kode_divisi' => $divisi,
                'no_invoice' => 'INV' . $i . substr(self::$testRunId, 0, 2),
                'tgl_faktur' => now()->toDateString(),
                'kode_cust' => $this->kodeCust,
                'kode_sales' => $this->kodeSales,
                'tipe' => '1',
                'jatuh_tempo' => now()->addDays(7)->toDateString(),
                'total' => 100,
                'disc' => 0,
                'pajak' => 10,
                'grand_total' => 110,
                'sisa_invoice' => 110,
                'ket' => 'Test',
                'status' => 'Open',
                'username' => 'tester',
                'tt' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $res = $this->getJson("/api/divisi/{$divisi}/invoices?per_page=2&search=INV");
        $res->assertStatus(200)
            ->assertJsonStructure([
                'data', 'summary', 'pagination', 'filters_applied', 'api_version', 'timestamp', 'endpoint', 'query_time'
            ]);
    }

    public function test_create_show_update_cancel_and_delete_rules(): void
    {
        $user = auth()->user();
        $divisi = $user->kode_divisi;

        $payload = [
            'no_invoice' => 'INVX' . substr(self::$testRunId, 0, 3),
            'tgl_faktur' => now()->toDateString(),
            'kode_cust' => $this->kodeCust,
            'kode_sales' => $this->kodeSales,
            'tipe' => '1',
            'jatuh_tempo' => now()->addDays(14)->toDateString(),
            'total' => 200,
            'disc' => 0,
            'pajak' => 20,
            'grand_total' => 220,
            'sisa_invoice' => 220,
            'ket' => 'Baru',
            'status' => 'Open',
            'username' => 'tester',
            'tt' => null,
        ];

        // create
        $res = $this->postJson("/api/divisi/{$divisi}/invoices", $payload);
        $res->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Invoice berhasil dibuat'
            ]);

        // show
        $res = $this->getJson("/api/divisi/{$divisi}/invoices/{$payload['no_invoice']}");
        $res->assertStatus(200)->assertJson([
            'success' => true,
            'data' => [ 'no_invoice' => strtoupper($payload['no_invoice']) ]
        ]);

        // update allowed when Open
    $update = [ 'ket' => 'Updated', 'total' => 210, 'grand_total' => 231, 'sisa_invoice' => 231 ];
        $res = $this->putJson("/api/divisi/{$divisi}/invoices/{$payload['no_invoice']}", $update);
        $res->assertStatus(200)->assertJson([
            'success' => true,
            'message' => 'Invoice berhasil diperbarui'
        ]);

        // cancel allowed when no payments (sisa == grand_total)
        $res = $this->patchJson("/api/divisi/{$divisi}/invoices/{$payload['no_invoice']}/cancel");
        $res->assertStatus(200)->assertJson([
            'success' => true,
            'message' => 'Invoice berhasil dibatalkan'
        ]);

        // delete should be blocked unless Open and sisa==grand_total and no details
        // After cancel, destroy should still succeed per controller? It requires status Open => expect 422
        $res = $this->deleteJson("/api/divisi/{$divisi}/invoices/{$payload['no_invoice']}");
        $res->assertStatus(422);
    }
}
