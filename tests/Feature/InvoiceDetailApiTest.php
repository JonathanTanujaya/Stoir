<?php

namespace Tests\Feature;

use Tests\TestCase;
use Tests\Traits\CreatesTestData;
use Illuminate\Foundation\Testing\WithFaker;

class InvoiceDetailApiTest extends TestCase
{
    use WithFaker, CreatesTestData;

    protected string $kodeCust;
    protected string $kodeSales;
    protected string $kodeBarang;
    protected string $noInvoice;

    protected function setUp(): void
    {
        parent::setUp();
        $user = $this->createTestUser();
        $this->actingAs($user);

        $this->kodeCust = 'C' . strtoupper(substr(self::$testRunId, 0, 4));
        $this->kodeSales = 'S' . strtoupper(substr(self::$testRunId, 0, 4));
        $this->kodeBarang = 'B' . strtoupper(substr(self::$testRunId, 0, 4));
        $this->noInvoice = 'INV' . strtoupper(substr(self::$testRunId, 0, 4));

        $area = $this->createTestArea();

        $custData = [
            'kode_cust' => $this->kodeCust,
            'nama_cust' => 'INVDET Customer',
            'kode_area' => $area['kode_area'],
            'alamat' => 'Jl. Test',
            'telp' => '0811111111',
            'status' => true,
        ];
        $existsCust = \DB::table('m_cust')->where('kode_cust', $this->kodeCust)->first();
        if ($existsCust) {
            \DB::table('m_cust')->where('kode_cust', $this->kodeCust)->update($custData);
        } else {
            \DB::table('m_cust')->insert($custData);
        }

        $salesData = [
            'kode_sales' => $this->kodeSales,
            'nama_sales' => 'INVDET Sales',
            'alamat' => 'Jl. Sales',
            'status' => true,
        ];
        $existsSales = \DB::table('m_sales')->where('kode_sales', $this->kodeSales)->first();
        if ($existsSales) {
            \DB::table('m_sales')->where('kode_sales', $this->kodeSales)->update($salesData);
        } else {
            \DB::table('m_sales')->insert($salesData);
        }

        $this->createTestKategori([
            'kode_kategori' => 'KAT01',
        ]);

        $barangData = [
            'kode_barang' => $this->kodeBarang,
            'nama_barang' => 'INVDET Barang',
            'kode_kategori' => 'KAT01',
            'harga_list' => 1000,
            'harga_jual' => 1200,
            'satuan' => 'PCS',
            'disc1' => 0,
            'disc2' => 0,
            'merk' => 'BRANDX',
            'barcode' => null,
            'status' => true,
            'lokasi' => 'R1',
            'stok_min' => 0,
        ];
        $existsBarang = \DB::table('m_barang')->where('kode_barang', $this->kodeBarang)->first();
        if ($existsBarang) {
            \DB::table('m_barang')->where('kode_barang', $this->kodeBarang)->update($barangData);
        } else {
            \DB::table('m_barang')->insert($barangData);
        }

        \DB::table('invoice')->insert([
            'no_invoice' => $this->noInvoice,
            'tgl_faktur' => now()->toDateString(),
            'kode_cust' => $this->kodeCust,
            'kode_sales' => $this->kodeSales,
            'tipe' => '1',
            'jatuh_tempo' => now()->addDays(7)->toDateString(),
            'total' => 0,
            'disc' => 0,
            'pajak' => 0,
            'grand_total' => 0,
            'sisa_invoice' => 0,
            'ket' => 'INVDET',
            'status' => 'Open',
            'username' => 'tester',
            'tt' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function test_list_create_show_update_delete_invoice_details(): void
    {
        $payload = [
            'kode_barang' => $this->kodeBarang,
            'qty_supply' => 2,
            'harga_jual' => 100,
            'jenis' => 'NORMAL',
            'diskon1' => 0,
            'diskon2' => 0,
            'harga_nett' => 200,
            'status' => 'OK',
        ];

        $res = $this->postJson("/api/invoices/{$this->noInvoice}/details", $payload);
        $res->assertStatus(201)->assertJsonFragment(['kode_barang' => $this->kodeBarang]);
        $id = $res->json('data.id') ?? $res->json('id');

        $res = $this->getJson("/api/invoices/{$this->noInvoice}/details?per_page=1&search={$this->kodeBarang}");
        $res->assertStatus(200)->assertJsonStructure([
            'data', 'summary', 'pagination', 'filters_applied', 'api_version', 'timestamp', 'endpoint', 'query_time'
        ]);

        $res = $this->getJson("/api/invoices/{$this->noInvoice}/details/{$id}");
        $res->assertStatus(200)->assertJsonFragment(['id' => $id]);

        $update = ['qty_supply' => 3, 'harga_nett' => 300];
        $res = $this->putJson("/api/invoices/{$this->noInvoice}/details/{$id}", $update);
        $res->assertStatus(200)->assertJsonFragment(['qty_supply' => 3]);

        $res = $this->deleteJson("/api/invoices/{$this->noInvoice}/details/{$id}");
        $res->assertStatus(204);
    }
}