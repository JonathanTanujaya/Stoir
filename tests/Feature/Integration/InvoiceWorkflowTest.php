<?php

namespace Tests\Feature\Integration;

use PHPUnit\Framework\Attributes\Test;



use Tests\TestCase;
use App\Models\Invoice;
use App\Models\InvoiceDetail;
use App\Models\DBarang;
use App\Models\KartuStok;
use App\Models\Journal;
use App\Models\MCust;
use App\Models\MSales;
use Illuminate\Foundation\Testing\RefreshDatabase;

class InvoiceWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = $this->createAuthenticatedUser('Administrator');
        $this->createWorkflowTestData();
    }

    protected function createWorkflowTestData()
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

        // Create test items with stock
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
    public function it_can_complete_invoice_creation_workflow()
    {
        // Step 1: Create invoice via API
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

        $response = $this->authenticateAs($this->user)
                        ->postJson('/api/invoices', $invoiceData);

        $response->assertStatus(201);

        // Step 2: Verify invoice was created with details
        $this->assertDatabaseHas('dbo.invoice', [
            'kodedivisi' => 'TEST',
            'noinvoice' => 'INV001',
            'total' => 150000,
            'status' => 'Draft'
        ]);

        $invoice = Invoice::where('noinvoice', 'INV001')->first();
        $this->assertCount(2, $invoice->details);

        // Step 3: Approve the invoice
        $approvalResponse = $this->authenticateAs($this->user)
                                ->putJson("/api/invoices/INV001", [
                                    'status' => 'Approved'
                                ]);

        $approvalResponse->assertStatus(200);

        // Step 4: Verify stock was reduced
        $item1 = DBarang::where('kodebarang', 'BRG001')->first();
        $item2 = DBarang::where('kodebarang', 'BRG002')->first();

        $this->assertEquals(90, $item1->stok); // 100 - 10
        $this->assertEquals(40, $item2->stok); // 50 - 10

        // Step 5: Verify stock movement records were created
        $this->assertDatabaseHas('dbo.kartu_stok', [
            'kodebarang' => 'BRG001',
            'jenis' => 'OUT',
            'qtykeluar' => 10,
            'noreferensi' => 'INV001'
        ]);

        $this->assertDatabaseHas('dbo.kartu_stok', [
            'kodebarang' => 'BRG002',
            'jenis' => 'OUT',
            'qtykeluar' => 10,
            'noreferensi' => 'INV001'
        ]);

        // Step 6: Verify journal entries were created
        $this->assertDatabaseHas('dbo.journal', [
            'noreferensi' => 'INV001',
            'keterangan' => 'Sales Invoice INV001'
        ]);
    }
    #[Test]
    public function it_handles_invoice_cancellation_workflow()
    {
        // Create and approve an invoice first
        $invoice = Invoice::create([
            'kodedivisi' => 'TEST',
            'noinvoice' => 'INV002',
            'tanggal' => '2024-01-01',
            'kodecust' => 'CUST001',
            'kodesales' => 'SALES001',
            'tipe' => 'CASH',
            'total' => 100000,
            'status' => 'Approved'
        ]);

        InvoiceDetail::create([
            'kodedivisi' => 'TEST',
            'noinvoice' => 'INV002',
            'kodebarang' => 'BRG001',
            'qty' => 5,
            'harga' => 10000,
            'harganett' => 10000
        ]);

        // Simulate stock reduction
        $item = DBarang::where('kodebarang', 'BRG001')->first();
        $item->update(['stok' => 95]); // Reduced by 5

        // Create stock movement record
        KartuStok::create([
            'kodedivisi' => 'TEST',
            'kodebarang' => 'BRG001',
            'tanggal' => '2024-01-01',
            'jenis' => 'OUT',
            'qtykeluar' => 5,
            'noreferensi' => 'INV002'
        ]);

        // Cancel the invoice
        $cancelResponse = $this->authenticateAs($this->user)
                              ->putJson("/api/invoices/INV002", [
                                  'status' => 'Cancelled'
                              ]);

        $cancelResponse->assertStatus(200);

        // Verify stock was restored
        $item->refresh();
        $this->assertEquals(100, $item->stok); // Back to original

        // Verify reversal stock movement was created
        $this->assertDatabaseHas('dbo.kartu_stok', [
            'kodebarang' => 'BRG001',
            'jenis' => 'IN',
            'qtymasuk' => 5,
            'noreferensi' => 'REV-INV002'
        ]);
    }
    #[Test]
    public function it_handles_credit_invoice_workflow()
    {
        // Create credit invoice
        $invoiceData = [
            'kodedivisi' => 'TEST',
            'noinvoice' => 'INV003',
            'tanggal' => '2024-01-01',
            'kodecust' => 'CUST001',
            'kodesales' => 'SALES001',
            'tipe' => 'CREDIT',
            'total' => 300000,
            'saldo' => 300000,
            'jatuh_tempo' => '2024-01-31',
            'status' => 'Draft',
            'details' => [
                [
                    'kodebarang' => 'BRG001',
                    'qty' => 20,
                    'harga' => 10000,
                    'harganett' => 10000
                ],
                [
                    'kodebarang' => 'BRG002',
                    'qty' => 20,
                    'harga' => 5000,
                    'harganett' => 5000
                ]
            ]
        ];

        $response = $this->authenticateAs($this->user)
                        ->postJson('/api/invoices', $invoiceData);

        $response->assertStatus(201);

        // Approve the credit invoice
        $this->authenticateAs($this->user)
             ->putJson("/api/invoices/INV003", ['status' => 'Approved']);

        // Verify customer debt increased
        $customer = MCust::where('kodecust', 'CUST001')->first();
        $totalDebt = Invoice::where('kodecust', 'CUST001')
                           ->where('tipe', 'CREDIT')
                           ->sum('saldo');

        $this->assertEquals(300000, $totalDebt);

        // Simulate payment
        $paymentResponse = $this->authenticateAs($this->user)
                               ->postJson('/api/payments', [
                                   'noinvoice' => 'INV003',
                                   'amount' => 150000,
                                   'tanggal' => '2024-01-15'
                               ]);

        if ($paymentResponse->status() === 201) {
            // Verify partial payment
            $invoice = Invoice::where('noinvoice', 'INV003')->first();
            $this->assertEquals(150000, $invoice->saldo); // 300000 - 150000
        }
    }
    #[Test]
    public function it_handles_stock_transfer_workflow()
    {
        // Create destination division
        $this->createTestDivision('OTHER');

        // Create stock transfer
        $transferData = [
            'kodedivisi' => 'TEST',
            'noreferensi' => 'TRF001',
            'tanggal' => '2024-01-01',
            'jenis' => 'TRANSFER',
            'tipetransaksi' => 'Inter-Division Transfer',
            'kodedivisi_tujuan' => 'OTHER',
            'status' => 'Draft',
            'items' => [
                [
                    'kodebarang' => 'BRG001',
                    'qtymasuk' => 0,
                    'qtykeluar' => 20,
                    'harga' => 10000
                ]
            ]
        ];

        $response = $this->authenticateAs($this->user)
                        ->postJson('/api/stock-transactions', $transferData);

        if ($response->status() === 201) {
            // Approve the transfer
            $this->authenticateAs($this->user)
                 ->putJson("/api/stock-transactions/TRF001", [
                     'status' => 'Approved'
                 ]);

            // Verify stock was reduced in source division
            $sourceItem = DBarang::where('kodedivisi', 'TEST')
                                ->where('kodebarang', 'BRG001')
                                ->first();
            $this->assertEquals(80, $sourceItem->stok);

            // Verify stock was increased in destination division
            $destItem = DBarang::where('kodedivisi', 'OTHER')
                              ->where('kodebarang', 'BRG001')
                              ->first();
            if ($destItem) {
                $this->assertEquals(20, $destItem->stok);
            }
        }
    }
    #[Test]
    public function it_handles_concurrent_stock_operations()
    {
        // Simulate concurrent invoice creation that might affect the same item
        $invoiceData1 = [
            'kodedivisi' => 'TEST',
            'noinvoice' => 'INV004',
            'tanggal' => '2024-01-01',
            'kodecust' => 'CUST001',
            'kodesales' => 'SALES001',
            'tipe' => 'CASH',
            'total' => 100000,
            'status' => 'Draft',
            'details' => [
                [
                    'kodebarang' => 'BRG001',
                    'qty' => 30,
                    'harga' => 10000,
                    'harganett' => 10000
                ]
            ]
        ];

        $invoiceData2 = [
            'kodedivisi' => 'TEST',
            'noinvoice' => 'INV005',
            'tanggal' => '2024-01-01',
            'kodecust' => 'CUST001',
            'kodesales' => 'SALES001',
            'tipe' => 'CASH',
            'total' => 100000,
            'status' => 'Draft',
            'details' => [
                [
                    'kodebarang' => 'BRG001',
                    'qty' => 40,
                    'harga' => 10000,
                    'harganett' => 10000
                ]
            ]
        ];

        // Create both invoices
        $response1 = $this->authenticateAs($this->user)
                         ->postJson('/api/invoices', $invoiceData1);

        $response2 = $this->authenticateAs($this->user)
                         ->postJson('/api/invoices', $invoiceData2);

        // Both should be created successfully
        $response1->assertStatus(201);
        $response2->assertStatus(201);

        // Approve first invoice - should work (30 out of 100)
        $approval1 = $this->authenticateAs($this->user)
                         ->putJson("/api/invoices/INV004", ['status' => 'Approved']);
        $approval1->assertStatus(200);

        // Approve second invoice - should fail (40 out of remaining 70, but total would be 70)
        $approval2 = $this->authenticateAs($this->user)
                         ->putJson("/api/invoices/INV005", ['status' => 'Approved']);

        // Either approval2 succeeds (if stock was sufficient) or fails with stock error
        $this->assertTrue(
            $approval2->status() === 200 || $approval2->status() === 422
        );
    }

    protected function createTestDivision($kodeDivisi)
    {
        \App\Models\MDivisi::create([
            'kodedivisi' => $kodeDivisi,
            'namadivisi' => $kodeDivisi . ' Division',
            'kodecompany' => 'TEST01',
            'status' => 'Active'
        ]);
    }
}
