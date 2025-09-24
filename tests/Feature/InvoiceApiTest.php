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

        $area = $this->createTestArea();
        $existsCust = \DB::table('m_cust')->where('kode_cust', $this->kodeCust)->first();
        $custData = [
            'kode_cust' => $this->kodeCust,
            'nama_cust' => 'INV Customer',
            'kode_area' => $area['kode_area'],
            'alamat' => 'Jl. Test',
            'telp' => '0811111111',
            'status' => true,
        ];
        if ($existsCust) {
            \DB::table('m_cust')->where('kode_cust', $this->kodeCust)->update($custData);
        } else {
            \DB::table('m_cust')->insert($custData);
        }
        $existsSales = \DB::table('m_sales')->where('kode_sales', $this->kodeSales)->first();
        $salesData = [
            'kode_sales' => $this->kodeSales,
            'nama_sales' => 'INV Sales',
            'alamat' => 'Jl. Sales',
            'status' => true,
        ];
        if ($existsSales) {
            \DB::table('m_sales')->where('kode_sales', $this->kodeSales)->update($salesData);
        } else {
            \DB::table('m_sales')->insert($salesData);
        }
    }

    public function test_list_invoices_with_filters_and_pagination(): void
    {
        foreach (range(1, 3) as $i) {
            \DB::table('invoice')->insert([
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

        $res = $this->getJson("/api/invoices?per_page=2&search=INV");
        $res->assertStatus(200)
            ->assertJsonStructure([
                'data', 'summary', 'pagination', 'filters_applied', 'api_version', 'timestamp', 'endpoint', 'query_time'
            ]);
    }

    public function test_create_show_update_cancel_and_delete_rules(): void
    {
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

        $res = $this->postJson("/api/invoices", $payload);
        $res->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Invoice berhasil dibuat'
            ]);

        $res = $this->getJson("/api/invoices/{$payload['no_invoice']}");
        $res->assertStatus(200)->assertJson([
            'success' => true,
            'data' => [ 'no_invoice' => strtoupper($payload['no_invoice']) ]
        ]);

    $update = [ 'ket' => 'Updated', 'total' => 210, 'grand_total' => 231, 'sisa_invoice' => 231 ];
        $res = $this->putJson("/api/invoices/{$payload['no_invoice']}", $update);
        $res->assertStatus(200)->assertJson([
            'success' => true,
            'message' => 'Invoice berhasil diperbarui'
        ]);

        $res = $this->patchJson("/api/invoices/{$payload['no_invoice']}/cancel");
        $res->assertStatus(200)->assertJson([
            'success' => true,
            'message' => 'Invoice berhasil dibatalkan'
        ]);

        $res = $this->deleteJson("/api/invoices/{$payload['no_invoice']}");
        $res->assertStatus(422);
    }
}