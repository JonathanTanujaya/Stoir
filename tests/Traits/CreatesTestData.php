<?php

namespace Tests\Traits;

use App\Models\MasterUser;
use App\Models\MCust;
use App\Models\MSales;
use App\Models\DBarang;
use App\Models\MDivisi;
use App\Models\Company;

trait CreatesTestData
{
    /**
     * Create test company
     */
    protected function createTestCompany($kodeCompany = 'TEST01'): Company
    {
        return Company::create([
            'kodecompany' => $kodeCompany,
            'namacompany' => 'Test Company ' . $kodeCompany,
            'alamat' => 'Test Address',
            'status' => 'Active'
        ]);
    }

    /**
     * Create test division
     */
    protected function createTestDivision($kodeDivisi = 'TEST', $kodeCompany = 'TEST01'): MDivisi
    {
        return MDivisi::create([
            'kodedivisi' => $kodeDivisi,
            'namadivisi' => 'Test Division ' . $kodeDivisi,
            'kodecompany' => $kodeCompany,
            'status' => 'Active'
        ]);
    }

    /**
     * Create test user
     */
    protected function createTestUser($attributes = []): MasterUser
    {
        $defaults = [
            'username' => 'testuser_' . uniqid(),
            'password' => 'password123',
            'nama' => 'Test User',
            'email' => 'test_' . uniqid() . '@test.com',
            'level' => 'User',
            'status' => 'Active',
            'kodedivisi' => 'TEST'
        ];

        return MasterUser::create(array_merge($defaults, $attributes));
    }

    /**
     * Create test customer
     */
    protected function createTestCustomer($attributes = []): MCust
    {
        $defaults = [
            'kodecust' => 'CUST' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT),
            'namacust' => 'Test Customer',
            'alamat' => 'Test Address',
            'credit_limit' => 1000000,
            'tipe_harga' => 'GROSIR',
            'status' => 'Active'
        ];

        return MCust::create(array_merge($defaults, $attributes));
    }

    /**
     * Create test sales person
     */
    protected function createTestSales($attributes = []): MSales
    {
        $defaults = [
            'kodesales' => 'SALES' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT),
            'namasales' => 'Test Sales Person',
            'status' => 'Active'
        ];

        return MSales::create(array_merge($defaults, $attributes));
    }

    /**
     * Create test item
     */
    protected function createTestItem($attributes = []): DBarang
    {
        $defaults = [
            'kodedivisi' => 'TEST',
            'kodebarang' => 'BRG' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT),
            'namabarang' => 'Test Item',
            'stok' => 100,
            'harga_jual' => 10000,
            'harga_minimum' => 8000,
            'harga_grosir' => 9000,
            'harga_eceran' => 11000,
            'status' => 'Active'
        ];

        return DBarang::create(array_merge($defaults, $attributes));
    }

    /**
     * Create multiple test items
     */
    protected function createTestItems($count = 3, $attributes = []): \Illuminate\Support\Collection
    {
        $items = collect();
        
        for ($i = 1; $i <= $count; $i++) {
            $itemAttributes = array_merge($attributes, [
                'kodebarang' => 'BRG' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'namabarang' => 'Test Item ' . $i,
            ]);
            
            $items->push($this->createTestItem($itemAttributes));
        }

        return $items;
    }

    /**
     * Create test data set for invoice testing
     */
    protected function createInvoiceTestData(): array
    {
        $customer = $this->createTestCustomer([
            'kodecust' => 'CUST001',
            'credit_limit' => 5000000
        ]);

        $sales = $this->createTestSales([
            'kodesales' => 'SALES001'
        ]);

        $items = $this->createTestItems(3, [
            'kodedivisi' => 'TEST'
        ]);

        return [
            'customer' => $customer,
            'sales' => $sales,
            'items' => $items
        ];
    }

    /**
     * Create test data set for stock transaction testing
     */
    protected function createStockTestData(): array
    {
        $items = $this->createTestItems(5, [
            'kodedivisi' => 'TEST',
            'stok' => 100
        ]);

        return [
            'items' => $items
        ];
    }
}
