<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Opname;
use App\Models\OpnameDetail;
use App\Models\MDivisi;
use App\Models\DBarang;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OpnameTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Override table names for testing (remove dbo schema)
        app()->bind(Opname::class, function() {
            $opname = new Opname();
            $opname->setTable('opname');
            return $opname;
        });
        
        // Create test items
        DBarang::create([
            'kodedivisi' => 'TEST',
            'kodebarang' => 'BRG001',
            'namabarang' => 'Test Item 1',
            'stok' => 100,
            'harga_jual' => 10000,
            'status' => 'Active'
        ]);

        DBarang::create([
            'kodedivisi' => 'TEST',
            'kodebarang' => 'BRG002',
            'namabarang' => 'Test Item 2',
            'stok' => 50,
            'harga_jual' => 5000,
            'status' => 'Active'
        ]);
    }

    public function test_it_can_create_opname()
    {
        $opname = new Opname();
        $opname->setTable('opname'); // Override for testing
        
        $opname = $opname->create([
            'kodedivisi' => 'TEST',
            'noopname' => 'OPN001',
            'tanggal' => '2024-01-01',
            'total' => 1500000
        ]);

        $this->assertInstanceOf(Opname::class, $opname);
        $this->assertEquals('TEST', $opname->kodedivisi);
        $this->assertEquals('OPN001', $opname->noopname);
        $this->assertEquals('2024-01-01', $opname->tanggal->format('Y-m-d'));
        $this->assertEquals(1500000, $opname->total);
    }

    public function test_it_has_composite_primary_key()
    {
        $opname = new Opname();
        $opname->setTable('opname');
        
        $opname = $opname->create([
            'kodedivisi' => 'TEST',
            'noopname' => 'OPN001',
            'tanggal' => '2024-01-01',
            'total' => 1500000
        ]);

        $this->assertEquals(['kodedivisi', 'noopname'], $opname->getKeyName());
        $this->assertFalse($opname->incrementing);
    }

    public function test_it_casts_attributes_correctly()
    {
        $opname = new Opname();
        $opname->setTable('opname');
        
        $opname = $opname->create([
            'kodedivisi' => 'TEST',
            'noopname' => 'OPN001',
            'tanggal' => '2024-01-01',
            'total' => '1500000.5000'
        ]);

        $this->assertInstanceOf(\Carbon\Carbon::class, $opname->tanggal);
        $this->assertEquals('1500000.5000', $opname->total);
    }

    public function test_basic_model_functionality()
    {
        $opname = new Opname();
        $opname->setTable('opname');
        
        // Test fillable fields
        $this->assertContains('kodedivisi', $opname->getFillable());
        $this->assertContains('noopname', $opname->getFillable());
        $this->assertContains('tanggal', $opname->getFillable());
        $this->assertContains('total', $opname->getFillable());
        
        // Test timestamps are disabled
        $this->assertFalse($opname->timestamps);
        
        // Test table name
        $this->assertEquals('opname', $opname->getTable());
    }
}
