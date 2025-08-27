<?php

namespace Tests\Feature\Api;

use PHPUnit\Framework\Attributes\Test;



use Tests\TestCase;
use App\Models\MasterUser;
use App\Models\MCust;
use App\Models\MSales;
use App\Models\DBarang;
use App\Models\Invoice;
use Illuminate\Foundation\Testing\RefreshDatabase;

class InvoiceApiTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $headers;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = $this->createAuthenticatedUser('Administrator');
        $this->headers = $this->authenticateAs($this->user);
        
        $this->createInvoiceTestData();
    }

    protected function createInvoiceTestData()
    {
        // Create test customer
        MCust::create([
            'kodecust' => 'CUST001',
            'namacust' => 'Test Customer',
            'alamat' => 'Test Address',
            'credit_limit' => 5000000,
            'status' => 'Active'
        ]);

        // Create test sales person
        MSales::create([
            'kodesales' => 'SALES001',
            'namasales' => 'Test Sales',
            'status' => 'Active'
        ]);

        // Create test items
        DBarang::create([
            'kodedivisi' => 'TEST',
            'kodebarang' => 'BRG001',
            'namabarang' => 'Test Item 1',
            'stok' => 100,
            'harga_jual' => 10000,
            'harga_minimum' => 8000,
            'status' => 'Active'
        ]);

        DBarang::create([
            'kodedivisi' => 'TEST',
            'kodebarang' => 'BRG002',
            'namabarang' => 'Test Item 2',
            'stok' => 50,
            'harga_jual' => 5000,
            'harga_minimum' => 4000,
            'status' => 'Active'
        ]);
    }
    #[Test]
    public function it_can_create_valid_invoice()
    {
        $invoiceData = [
            'kodedivisi' => 'TEST',
            'noinvoice' => 'INV001',
            'tanggal' => '2024-01-01',
            'kodecust' => 'CUST001',
            'kodesales' => 'SALES001',
            'tipe' => 'CASH',
            'total' => 150000,
            'status' => 'Draft',
            'details' => [
                [
                    'kodebarang' => 'BRG001',
                    'qty' => 10,
                    'harga' => 10000,
                    'harganett' => 10000
                ],
                [
                    'kodebarang' => 'BRG002',
                    'qty' => 10,
                    'harga' => 5000,
                    'harganett' => 5000
                ]
            ]
        ];

        $response = $this->postJson('/api/invoices', $invoiceData);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'message',
                    'data' => [
                        'kodedivisi',
                        'noinvoice',
                        'total',
                        'details'
                    ]
                ]);

        $this->assertDatabaseHas('dbo.invoice', [
            'kodedivisi' => 'TEST',
            'noinvoice' => 'INV001',
            'total' => 150000
        ]);
    }
    #[Test]
    public function it_validates_required_fields()
    {
        $invalidData = [
            'kodedivisi' => '',
            'noinvoice' => '',
            'tanggal' => '',
            'kodecust' => '',
            'kodesales' => '',
            'tipe' => '',
            'total' => '',
            'status' => ''
        ];

        $response = $this->postJson('/api/invoices', $invalidData);

        $response->assertStatus(422)
                ->assertJsonValidationErrors([
                    'kodedivisi',
                    'noinvoice',
                    'tanggal',
                    'kodecust',
                    'kodesales',
                    'tipe',
                    'total',
                    'status'
                ]);
    }
    #[Test]
    public function it_validates_stock_availability()
    {
        $invoiceData = [
            'kodedivisi' => 'TEST',
            'noinvoice' => 'INV002',
            'tanggal' => '2024-01-01',
            'kodecust' => 'CUST001',
            'kodesales' => 'SALES001',
            'tipe' => 'CASH',
            'total' => 1500000,
            'status' => 'Draft',
            'details' => [
                [
                    'kodebarang' => 'BRG001',
                    'qty' => 150, // Exceeds available stock (100)
                    'harga' => 10000,
                    'harganett' => 10000
                ]
            ]
        ];

        $response = $this->postJson('/api/invoices', $invoiceData);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['details.0.qty']);
    }
    #[Test]
    public function it_validates_credit_limit()
    {
        $invoiceData = [
            'kodedivisi' => 'TEST',
            'noinvoice' => 'INV003',
            'tanggal' => '2024-01-01',
            'kodecust' => 'CUST001',
            'kodesales' => 'SALES001',
            'tipe' => 'CREDIT',
            'total' => 6000000, // Exceeds credit limit (5M)
            'status' => 'Draft',
            'details' => [
                [
                    'kodebarang' => 'BRG001',
                    'qty' => 50,
                    'harga' => 10000,
                    'harganett' => 10000
                ]
            ]
        ];

        $response = $this->postJson('/api/invoices', $invoiceData);

        $response->assertStatus(422);
        // Should contain business rule error about credit limit
    }
    #[Test]
    public function it_validates_minimum_price()
    {
        $invoiceData = [
            'kodedivisi' => 'TEST',
            'noinvoice' => 'INV004',
            'tanggal' => '2024-01-01',
            'kodecust' => 'CUST001',
            'kodesales' => 'SALES001',
            'tipe' => 'CASH',
            'total' => 70000,
            'status' => 'Draft',
            'details' => [
                [
                    'kodebarang' => 'BRG001',
                    'qty' => 10,
                    'harga' => 7000, // Below minimum (8000)
                    'harganett' => 7000
                ]
            ]
        ];

        $response = $this->postJson('/api/invoices', $invoiceData);

        $response->assertStatus(422);
        // Should contain business rule error about minimum price
    }
    #[Test]
    public function it_requires_authentication()
    {
        $invoiceData = [
            'kodedivisi' => 'TEST',
            'noinvoice' => 'INV005',
            'tanggal' => '2024-01-01',
            'kodecust' => 'CUST001',
            'kodesales' => 'SALES001',
            'tipe' => 'CASH',
            'total' => 100000,
            'status' => 'Draft'
        ];

        $response = $this->postJson('/api/invoices', $invoiceData);

        $response->assertStatus(401);
    }
    #[Test]
    public function it_can_get_invoice_list()
    {
        // Create test invoices
        Invoice::create([
            'kodedivisi' => 'TEST',
            'noinvoice' => 'INV001',
            'tanggal' => '2024-01-01',
            'kodecust' => 'CUST001',
            'kodesales' => 'SALES001',
            'tipe' => 'CASH',
            'total' => 100000,
            'status' => 'Draft'
        ]);

        Invoice::create([
            'kodedivisi' => 'TEST',
            'noinvoice' => 'INV002',
            'tanggal' => '2024-01-02',
            'kodecust' => 'CUST001',
            'kodesales' => 'SALES001',
            'tipe' => 'CREDIT',
            'total' => 200000,
            'status' => 'Approved'
        ]);

        $response = $this->getJson('/api/invoices');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'kodedivisi',
                            'noinvoice',
                            'tanggal',
                            'total',
                            'status'
                        ]
                    ]
                ]);

        $this->assertCount(2, $response->json('data'));
    }
    #[Test]
    public function it_can_get_single_invoice()
    {
        $invoice = Invoice::create([
            'kodedivisi' => 'TEST',
            'noinvoice' => 'INV001',
            'tanggal' => '2024-01-01',
            'kodecust' => 'CUST001',
            'kodesales' => 'SALES001',
            'tipe' => 'CASH',
            'total' => 100000,
            'status' => 'Draft'
        ]);

        $response = $this->getJson("/api/invoices/{$invoice->noinvoice}");

        $response->assertStatus(200)
                ->assertJson([
                    'data' => [
                        'kodedivisi' => 'TEST',
                        'noinvoice' => 'INV001',
                        'total' => 100000
                    ]
                ]);
    }
    #[Test]
    public function it_can_update_invoice()
    {
        $invoice = Invoice::create([
            'kodedivisi' => 'TEST',
            'noinvoice' => 'INV001',
            'tanggal' => '2024-01-01',
            'kodecust' => 'CUST001',
            'kodesales' => 'SALES001',
            'tipe' => 'CASH',
            'total' => 100000,
            'status' => 'Draft'
        ]);

        $updateData = [
            'status' => 'Approved',
            'keterangan' => 'Updated via API'
        ];

        $response = $this->putJson("/api/invoices/{$invoice->noinvoice}", $updateData);

        $response->assertStatus(200);

        $this->assertDatabaseHas('dbo.invoice', [
            'kodedivisi' => 'TEST',
            'noinvoice' => 'INV001',
            'status' => 'Approved',
            'keterangan' => 'Updated via API'
        ]);
    }
    #[Test]
    public function it_can_delete_invoice()
    {
        $invoice = Invoice::create([
            'kodedivisi' => 'TEST',
            'noinvoice' => 'INV001',
            'tanggal' => '2024-01-01',
            'kodecust' => 'CUST001',
            'kodesales' => 'SALES001',
            'tipe' => 'CASH',
            'total' => 100000,
            'status' => 'Draft'
        ]);

        $response = $this->deleteJson("/api/invoices/{$invoice->noinvoice}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('dbo.invoice', [
            'kodedivisi' => 'TEST',
            'noinvoice' => 'INV001'
        ]);
    }
    #[Test]
    public function it_respects_rate_limiting()
    {
        // Make multiple requests to test rate limiting
        for ($i = 0; $i < 125; $i++) {
            $this->getJson('/api/invoices');
        }

        // The 126th request should be rate limited
        $response = $this->getJson('/api/invoices');
        $response->assertStatus(429); // Too Many Requests
    }
    #[Test]
    public function it_sanitizes_input()
    {
        $maliciousData = [
            'kodedivisi' => 'TEST',
            'noinvoice' => 'INV<script>alert("xss")</script>001',
            'tanggal' => '2024-01-01',
            'kodecust' => 'CUST001',
            'kodesales' => 'SALES001',
            'tipe' => 'CASH',
            'total' => 100000,
            'status' => 'Draft',
            'keterangan' => '<script>alert("xss")</script>Test note'
        ];

        $response = $this->postJson('/api/invoices', $maliciousData);

        // Should sanitize script tags
        if ($response->status() === 201) {
            $this->assertDatabaseMissing('dbo.invoice', [
                'noinvoice' => 'INV<script>alert("xss")</script>001'
            ]);
        }
    }
}
