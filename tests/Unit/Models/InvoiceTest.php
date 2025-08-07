<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Invoice;
use App\Models\InvoiceDetail;
use App\Models\MCust;
use App\Models\MSales;
use App\Models\DBarang;
use Illuminate\Foundation\Testing\RefreshDatabase;

class InvoiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
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
    }

    /** @test */
    public function it_can_create_invoice()
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

        $this->assertInstanceOf(Invoice::class, $invoice);
        $this->assertEquals('TEST', $invoice->kodedivisi);
        $this->assertEquals('INV001', $invoice->noinvoice);
        $this->assertEquals('CASH', $invoice->tipe);
    }

    /** @test */
    public function it_has_composite_primary_key()
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

        $this->assertEquals(['kodedivisi', 'noinvoice'], $invoice->getKeyName());
        $this->assertFalse($invoice->incrementing);
    }

    /** @test */
    public function it_belongs_to_customer()
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

        $this->assertInstanceOf(MCust::class, $invoice->customer);
        $this->assertEquals('CUST001', $invoice->customer->kodecust);
    }

    /** @test */
    public function it_belongs_to_sales_person()
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

        $this->assertInstanceOf(MSales::class, $invoice->sales);
        $this->assertEquals('SALES001', $invoice->sales->kodesales);
    }

    /** @test */
    public function it_can_have_details()
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

        InvoiceDetail::create([
            'kodedivisi' => 'TEST',
            'noinvoice' => 'INV001',
            'kodebarang' => 'BRG001',
            'qty' => 10,
            'harga' => 10000,
            'harganett' => 10000
        ]);

        $this->assertCount(1, $invoice->details);
        $this->assertEquals('BRG001', $invoice->details->first()->kodebarang);
    }

    /** @test */
    public function it_can_calculate_total_from_details()
    {
        $invoice = Invoice::create([
            'kodedivisi' => 'TEST',
            'noinvoice' => 'INV001',
            'tanggal' => '2024-01-01',
            'kodecust' => 'CUST001',
            'kodesales' => 'SALES001',
            'tipe' => 'CASH',
            'total' => 0,
            'status' => 'Draft'
        ]);

        InvoiceDetail::create([
            'kodedivisi' => 'TEST',
            'noinvoice' => 'INV001',
            'kodebarang' => 'BRG001',
            'qty' => 10,
            'harga' => 10000,
            'harganett' => 10000
        ]);

        $calculatedTotal = $invoice->calculateTotal();
        $this->assertEquals(100000, $calculatedTotal); // 10 * 10000
    }

    /** @test */
    public function it_can_scope_by_customer()
    {
        // Create another customer
        MCust::create([
            'kodecust' => 'CUST002',
            'namacust' => 'Another Customer',
            'alamat' => 'Another Address',
            'status' => 'Active'
        ]);

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
            'tanggal' => '2024-01-01',
            'kodecust' => 'CUST002',
            'kodesales' => 'SALES001',
            'tipe' => 'CASH',
            'total' => 200000,
            'status' => 'Draft'
        ]);

        $cust1Invoices = Invoice::byCustomer('CUST001')->get();
        $cust2Invoices = Invoice::byCustomer('CUST002')->get();

        $this->assertCount(1, $cust1Invoices);
        $this->assertCount(1, $cust2Invoices);
        $this->assertEquals('CUST001', $cust1Invoices->first()->kodecust);
        $this->assertEquals('CUST002', $cust2Invoices->first()->kodecust);
    }

    /** @test */
    public function it_can_scope_by_status()
    {
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
            'tanggal' => '2024-01-01',
            'kodecust' => 'CUST001',
            'kodesales' => 'SALES001',
            'tipe' => 'CASH',
            'total' => 200000,
            'status' => 'Approved'
        ]);

        $draftInvoices = Invoice::byStatus('Draft')->get();
        $approvedInvoices = Invoice::byStatus('Approved')->get();

        $this->assertCount(1, $draftInvoices);
        $this->assertCount(1, $approvedInvoices);
        $this->assertEquals('Draft', $draftInvoices->first()->status);
        $this->assertEquals('Approved', $approvedInvoices->first()->status);
    }

    /** @test */
    public function it_can_scope_pending_invoices()
    {
        Invoice::create([
            'kodedivisi' => 'TEST',
            'noinvoice' => 'INV001',
            'tanggal' => '2024-01-01',
            'kodecust' => 'CUST001',
            'kodesales' => 'SALES001',
            'tipe' => 'CREDIT',
            'total' => 100000,
            'saldo' => 100000,
            'status' => 'Approved'
        ]);

        Invoice::create([
            'kodedivisi' => 'TEST',
            'noinvoice' => 'INV002',
            'tanggal' => '2024-01-01',
            'kodecust' => 'CUST001',
            'kodesales' => 'SALES001',
            'tipe' => 'CREDIT',
            'total' => 200000,
            'saldo' => 0, // Paid
            'status' => 'Approved'
        ]);

        $pendingInvoices = Invoice::pending()->get();

        $this->assertCount(1, $pendingInvoices);
        $this->assertEquals('INV001', $pendingInvoices->first()->noinvoice);
        $this->assertEquals(100000, $pendingInvoices->first()->saldo);
    }

    /** @test */
    public function it_can_check_if_paid()
    {
        $paidInvoice = Invoice::create([
            'kodedivisi' => 'TEST',
            'noinvoice' => 'INV001',
            'tanggal' => '2024-01-01',
            'kodecust' => 'CUST001',
            'kodesales' => 'SALES001',
            'tipe' => 'CREDIT',
            'total' => 100000,
            'saldo' => 0,
            'status' => 'Approved'
        ]);

        $unpaidInvoice = Invoice::create([
            'kodedivisi' => 'TEST',
            'noinvoice' => 'INV002',
            'tanggal' => '2024-01-01',
            'kodecust' => 'CUST001',
            'kodesales' => 'SALES001',
            'tipe' => 'CREDIT',
            'total' => 200000,
            'saldo' => 100000,
            'status' => 'Approved'
        ]);

        $this->assertTrue($paidInvoice->isPaid());
        $this->assertFalse($unpaidInvoice->isPaid());
    }

    /** @test */
    public function it_can_check_if_overdue()
    {
        $overdueInvoice = Invoice::create([
            'kodedivisi' => 'TEST',
            'noinvoice' => 'INV001',
            'tanggal' => '2024-01-01',
            'kodecust' => 'CUST001',
            'kodesales' => 'SALES001',
            'tipe' => 'CREDIT',
            'total' => 100000,
            'saldo' => 100000,
            'jatuh_tempo' => '2024-01-15', // Overdue
            'status' => 'Approved'
        ]);

        $currentInvoice = Invoice::create([
            'kodedivisi' => 'TEST',
            'noinvoice' => 'INV002',
            'tanggal' => '2024-01-01',
            'kodecust' => 'CUST001',
            'kodesales' => 'SALES001',
            'tipe' => 'CREDIT',
            'total' => 200000,
            'saldo' => 200000,
            'jatuh_tempo' => now()->addDays(30)->format('Y-m-d'), // Future
            'status' => 'Approved'
        ]);

        $this->assertTrue($overdueInvoice->isOverdue());
        $this->assertFalse($currentInvoice->isOverdue());
    }

    /** @test */
    public function it_casts_attributes_correctly()
    {
        $invoice = Invoice::create([
            'kodedivisi' => 'TEST',
            'noinvoice' => 'INV001',
            'tanggal' => '2024-01-01',
            'kodecust' => 'CUST001',
            'kodesales' => 'SALES001',
            'tipe' => 'CASH',
            'total' => '100000.50',
            'diskon' => '5.25',
            'status' => 'Draft'
        ]);

        $this->assertInstanceOf(\Carbon\Carbon::class, $invoice->tanggal);
        $this->assertEquals('100000.5000', $invoice->total);
        $this->assertEquals('5.25', $invoice->diskon);
    }
}
