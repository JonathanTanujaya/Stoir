<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\BusinessRuleService;
use App\Models\MCust;
use App\Models\DBarang;
use App\Models\Invoice;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BusinessRuleServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $businessRuleService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->businessRuleService = new BusinessRuleService();

        // Create test data
        $this->createBusinessRuleTestData();
    }

    protected function createBusinessRuleTestData()
    {
        // Create test customer
        MCust::create([
            'kodecust' => 'CUST001',
            'namacust' => 'Test Customer',
            'alamat' => 'Test Address',
            'credit_limit' => 1000000, // 1M credit limit
            'tipe_harga' => 'GROSIR',
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
            'harga_grosir' => 9000,
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

        // Create existing credit invoice
        Invoice::create([
            'kodedivisi' => 'TEST',
            'noinvoice' => 'INV001',
            'tanggal' => '2024-01-01',
            'kodecust' => 'CUST001',
            'kodesales' => 'SALES001',
            'tipe' => 'CREDIT',
            'total' => 500000,
            'saldo' => 300000, // 300K outstanding
            'status' => 'Approved'
        ]);
    }

    /** @test */
    public function it_validates_customer_credit_limit()
    {
        // Test within credit limit
        $error = $this->businessRuleService->validateCustomerCredit('CUST001', 500000);
        $this->assertNull($error);

        // Test exceeding credit limit
        $error = $this->businessRuleService->validateCustomerCredit('CUST001', 800000);
        $this->assertNotNull($error);
        $this->assertStringContainsString('Credit limit exceeded', $error);
    }

    /** @test */
    public function it_validates_customer_with_no_credit_limit()
    {
        // Create customer with no credit limit
        MCust::create([
            'kodecust' => 'CUST002',
            'namacust' => 'No Limit Customer',
            'credit_limit' => 0,
            'status' => 'Active'
        ]);

        $error = $this->businessRuleService->validateCustomerCredit('CUST002', 5000000);
        $this->assertNull($error); // Should allow any amount
    }

    /** @test */
    public function it_validates_stock_availability()
    {
        $items = [
            [
                'kodebarang' => 'BRG001',
                'qty' => 50 // Within stock
            ],
            [
                'kodebarang' => 'BRG002',
                'qty' => 60 // Exceeds stock (50 available)
            ]
        ];

        $errors = $this->businessRuleService->validateStockAvailability($items);

        $this->assertEmpty($errors[0] ?? null); // First item should be valid
        $this->assertNotEmpty($errors[1]); // Second item should have error
        $this->assertStringContainsString('Insufficient stock', $errors[1]);
    }

    /** @test */
    public function it_validates_inactive_items()
    {
        // Create inactive item
        DBarang::create([
            'kodedivisi' => 'TEST',
            'kodebarang' => 'BRG003',
            'namabarang' => 'Inactive Item',
            'stok' => 100,
            'status' => 'Inactive'
        ]);

        $items = [
            [
                'kodebarang' => 'BRG003',
                'qty' => 10
            ]
        ];

        $errors = $this->businessRuleService->validateStockAvailability($items);

        $this->assertNotEmpty($errors[0]);
        $this->assertStringContainsString('not active', $errors[0]);
    }

    /** @test */
    public function it_validates_pricing_rules()
    {
        $items = [
            [
                'kodebarang' => 'BRG001',
                'harga' => 9000 // Valid grosir price
            ],
            [
                'kodebarang' => 'BRG002',
                'harga' => 3000 // Below minimum (4000)
            ]
        ];

        $errors = $this->businessRuleService->validatePricingRules($items, 'CUST001');

        $this->assertEmpty($errors[0] ?? null); // First item should be valid
        $this->assertNotEmpty($errors[1]); // Second item should have error
        $this->assertStringContainsString('Price below minimum', $errors[1]);
    }

    /** @test */
    public function it_validates_customer_specific_pricing()
    {
        $items = [
            [
                'kodebarang' => 'BRG001',
                'harga' => 12000 // Significantly above grosir price (9000)
            ]
        ];

        $errors = $this->businessRuleService->validatePricingRules($items, 'CUST001');

        $this->assertNotEmpty($errors[0]);
        $this->assertStringContainsString('Price deviation', $errors[0]);
    }

    /** @test */
    public function it_validates_maximum_transaction_amount()
    {
        // Test within limit
        $error = $this->businessRuleService->validateMaxTransactionAmount(5000000);
        $this->assertNull($error);

        // Test exceeding limit (default 10M from config)
        $error = $this->businessRuleService->validateMaxTransactionAmount(15000000);
        $this->assertNotNull($error);
        $this->assertStringContainsString('exceeds maximum limit', $error);
    }

    /** @test */
    public function it_validates_complete_invoice()
    {
        $invoiceData = [
            'kodecust' => 'CUST001',
            'tipe' => 'CREDIT',
            'total' => 600000, // Within credit limit
            'details' => [
                [
                    'kodebarang' => 'BRG001',
                    'qty' => 30,
                    'harga' => 9000
                ],
                [
                    'kodebarang' => 'BRG002',
                    'qty' => 20,
                    'harga' => 4500
                ]
            ]
        ];

        $errors = $this->businessRuleService->validateInvoice($invoiceData);

        $this->assertEmpty($errors); // Should be valid
    }

    /** @test */
    public function it_validates_invalid_invoice()
    {
        $invoiceData = [
            'kodecust' => 'CUST001',
            'tipe' => 'CREDIT',
            'total' => 800000, // Exceeds credit limit
            'details' => [
                [
                    'kodebarang' => 'BRG001',
                    'qty' => 150, // Exceeds stock
                    'harga' => 7000 // Below minimum
                ]
            ]
        ];

        $errors = $this->businessRuleService->validateInvoice($invoiceData);

        $this->assertNotEmpty($errors);
        $this->assertArrayHasKey('credit_limit', $errors);
        $this->assertArrayHasKey('stock', $errors);
        $this->assertArrayHasKey('pricing', $errors);
    }

    /** @test */
    public function it_validates_transaction_timing()
    {
        // Test valid date
        $error = $this->businessRuleService->validateTransactionTiming('2024-01-01');
        $this->assertNull($error);

        // Test future date
        $futureDate = now()->addDays(1)->format('Y-m-d');
        $error = $this->businessRuleService->validateTransactionTiming($futureDate);
        $this->assertNotNull($error);
        $this->assertStringContainsString('cannot be in the future', $error);

        // Test very old date
        $oldDate = now()->subDays(400)->format('Y-m-d');
        $error = $this->businessRuleService->validateTransactionTiming($oldDate);
        $this->assertNotNull($error);
        $this->assertStringContainsString('more than', $error);
    }

    /** @test */
    public function it_validates_stock_movements()
    {
        $items = [
            [
                'kodebarang' => 'BRG001',
                'qtymasuk' => 0,
                'qtykeluar' => 30 // Valid outgoing
            ],
            [
                'kodebarang' => 'BRG002',
                'qtymasuk' => 0,
                'qtykeluar' => 60 // Exceeds available stock
            ]
        ];

        $errors = $this->businessRuleService->validateStockMovements($items, 'OUT');

        $this->assertEmpty($errors[0] ?? null); // First item should be valid
        $this->assertNotEmpty($errors[1]); // Second item should have error
        $this->assertStringContainsString('Insufficient stock', $errors[1]);
    }

    /** @test */
    public function it_validates_transfer_rules()
    {
        $transferData = [
            'kodedivisi' => 'TEST',
            'kodedivisi_tujuan' => 'TEST' // Same division
        ];

        $error = $this->businessRuleService->validateTransferRules($transferData);
        $this->assertNotNull($error);
        $this->assertStringContainsString('cannot be the same', $error);

        // Valid transfer
        $transferData['kodedivisi_tujuan'] = 'OTHER';
        // This would need OTHER division to exist for full validation
    }

    /** @test */
    public function it_validates_journal_balance()
    {
        $balancedDetails = [
            ['debit' => 100000, 'kredit' => 0],
            ['debit' => 0, 'kredit' => 100000]
        ];

        $error = $this->businessRuleService->validateJournalBalance($balancedDetails);
        $this->assertNull($error);

        $unbalancedDetails = [
            ['debit' => 100000, 'kredit' => 0],
            ['debit' => 0, 'kredit' => 90000]
        ];

        $error = $this->businessRuleService->validateJournalBalance($unbalancedDetails);
        $this->assertNotNull($error);
        $this->assertStringContainsString('not balanced', $error);
    }
}
