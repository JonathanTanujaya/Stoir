<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{
    User, MCust, MSales, MBarang, MKategori, MArea, MSupplier, 
    Invoice, InvoiceDetail, DBarang, KartuStok, PartPenerimaan, 
    PartPenerimaanDetail, ReturnSales, ReturnSalesDetail, 
    PenerimaanFinance, PenerimaanFinanceDetail, Company, 
    MResi, MTT, DTT, MBank, DBank, MVoucher, DVoucher,
    ClaimPenjualan, ClaimPenjualanDetail, PartPenerimaanBonus,
    PartPenerimaanBonusDetail, ReturPenerimaan, ReturPenerimaanDetail,
    SaldoBank, SPV, StokClaim, StokMinimum, UserModule,
    MDivisi, MDokumen, MModule, MasterUser, MergeBarang,
    MergeBarangDetail, Opname, OpnameDetail, MCOA, Journal,
    MTrans, DTrans, MCustDiskonDetail
};
use Illuminate\Support\Facades\Hash;

class ComprehensiveDataSeeder extends Seeder
{
    public function run(): void
    {
        // Core Master Data
        $this->seedUsers();
        $this->seedCompany();
        $this->seedCategories();
        $this->seedAreas();
        $this->seedSuppliers();
        $this->seedCustomers();
        $this->seedSales();
        $this->seedProducts();
        $this->seedBanks();
        $this->seedDivisions();
        $this->seedModules();
        $this->seedDocuments();
        $this->seedCOA();
        
        // Transaction Data
        $this->seedInvoices();
        $this->seedPurchases();
        $this->seedStock();
        $this->seedFinancial();
        
        // Supporting Data
        $this->seedVouchers();
        $this->seedClaims();
        $this->seedOpname();
        $this->seedJournal();
    }

    private function seedUsers(): void
    {
        for ($i = 1; $i <= 5; $i++) {
            User::create([
                'name' => "User {$i}",
                'email' => "user{$i}@stoir.com",
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
            ]);
        }
    }

    private function seedCompany(): void
    {
        for ($i = 1; $i <= 5; $i++) {
            Company::create([
                'name' => "PT Stoir Company {$i}",
                'address' => "Jl. Bisnis No. {$i}",
                'phone' => "021-555-000{$i}",
                'email' => "company{$i}@stoir.com",
                'npwp' => "12.345.678.{$i}-123.000",
            ]);
        }
    }

    private function seedCategories(): void
    {
        $categories = ['Elektronik', 'Fashion', 'Makanan', 'Minuman', 'Otomotif'];
        foreach ($categories as $index => $category) {
            MKategori::create([
                'kode' => 'KAT' . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                'kategori' => $category,
                'keterangan' => "Kategori {$category}",
            ]);
        }
    }

    private function seedAreas(): void
    {
        $areas = ['Jakarta', 'Bandung', 'Surabaya', 'Medan', 'Makassar'];
        foreach ($areas as $index => $area) {
            MArea::create([
                'kode' => 'ARE' . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                'area' => $area,
                'keterangan' => "Area {$area}",
            ]);
        }
    }

    private function seedSuppliers(): void
    {
        for ($i = 1; $i <= 5; $i++) {
            MSupplier::create([
                'kode' => 'SUP' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'nama' => "Supplier {$i}",
                'alamat' => "Jl. Supplier No. {$i}",
                'telepon' => "021-111-000{$i}",
                'email' => "supplier{$i}@email.com",
                'npwp' => "98.765.432.{$i}-987.000",
                'kontak_person' => "Contact {$i}",
            ]);
        }
    }

    private function seedCustomers(): void
    {
        for ($i = 1; $i <= 5; $i++) {
            MCust::create([
                'kode' => 'CUST' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'nama' => "Customer {$i}",
                'alamat' => "Jl. Customer No. {$i}",
                'telepon' => "021-222-000{$i}",
                'email' => "customer{$i}@email.com",
                'npwp' => "11.222.333.{$i}-444.000",
                'kontak_person' => "PIC {$i}",
                'limit_kredit' => 10000000 + ($i * 5000000),
            ]);
        }
    }

    private function seedSales(): void
    {
        for ($i = 1; $i <= 5; $i++) {
            MSales::create([
                'kode' => 'SAL' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'nama' => "Sales {$i}",
                'alamat' => "Jl. Sales No. {$i}",
                'telepon' => "021-333-000{$i}",
                'email' => "sales{$i}@stoir.com",
            ]);
        }
    }

    private function seedProducts(): void
    {
        $categories = MKategori::all();
        for ($i = 1; $i <= 5; $i++) {
            MBarang::create([
                'kode' => 'BRG' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'nama' => "Produk {$i}",
                'kategori_id' => $categories->random()->id,
                'satuan' => ['PCS', 'BOX', 'SET', 'UNIT', 'PAKET'][array_rand(['PCS', 'BOX', 'SET', 'UNIT', 'PAKET'])],
                'harga_beli' => 50000 + ($i * 25000),
                'harga_jual' => 75000 + ($i * 35000),
                'stok' => 100 + ($i * 50),
                'stok_minimum' => 10 + ($i * 5),
                'keterangan' => "Deskripsi produk {$i}",
            ]);
        }
    }

    private function seedBanks(): void
    {
        $banks = ['BCA', 'Mandiri', 'BNI', 'BRI', 'CIMB'];
        foreach ($banks as $index => $bank) {
            MBank::create([
                'kode' => 'BNK' . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                'nama' => "Bank {$bank}",
                'no_rekening' => '1234567890' . ($index + 1),
                'atas_nama' => "PT Stoir - {$bank}",
                'cabang' => "Cabang Jakarta {$bank}",
            ]);

            // Create bank details
            DBank::create([
                'bank_id' => MBank::latest()->first()->id,
                'tanggal' => now()->subDays($index),
                'keterangan' => "Saldo awal {$bank}",
                'debit' => 50000000 + ($index * 10000000),
                'kredit' => 0,
                'saldo' => 50000000 + ($index * 10000000),
            ]);
        }
    }

    private function seedDivisions(): void
    {
        $divisions = ['IT', 'Finance', 'Sales', 'Purchasing', 'Warehouse'];
        foreach ($divisions as $index => $division) {
            MDivisi::create([
                'kode' => 'DIV' . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                'nama' => $division,
                'keterangan' => "Divisi {$division}",
            ]);
        }
    }

    private function seedModules(): void
    {
        $modules = ['Master Data', 'Penjualan', 'Pembelian', 'Inventory', 'Finance'];
        foreach ($modules as $index => $module) {
            MModule::create([
                'kode' => 'MOD' . str_pad($index + 1, '0', STR_PAD_LEFT),
                'nama' => $module,
                'url' => strtolower(str_replace(' ', '-', $module)),
                'icon' => 'fas fa-' . strtolower(str_replace(' ', '-', $module)),
            ]);
        }
    }

    private function seedDocuments(): void
    {
        $documents = ['Invoice', 'Purchase Order', 'Delivery Order', 'Payment Voucher', 'Receipt'];
        foreach ($documents as $index => $document) {
            MDokumen::create([
                'kode' => 'DOK' . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                'nama' => $document,
                'template' => strtolower(str_replace(' ', '_', $document)) . '.pdf',
                'keterangan' => "Template {$document}",
            ]);
        }
    }

    private function seedCOA(): void
    {
        $coas = [
            ['1000', 'ASET', 'Aset'],
            ['1100', 'KAS DAN BANK', 'Kas dan Bank'],
            ['2000', 'KEWAJIBAN', 'Kewajiban'],
            ['3000', 'MODAL', 'Modal'],
            ['4000', 'PENDAPATAN', 'Pendapatan']
        ];

        foreach ($coas as $coa) {
            MCOA::create([
                'kode' => $coa[0],
                'nama' => $coa[1],
                'jenis' => $coa[2],
                'level' => strlen($coa[0]) / 2,
            ]);
        }
    }

    private function seedInvoices(): void
    {
        $customers = MCust::all();
        $sales = MSales::all();
        $products = MBarang::all();

        for ($i = 1; $i <= 5; $i++) {
            $invoice = Invoice::create([
                'nomor' => 'INV-' . date('Ymd') . '-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'tanggal' => now()->subDays(rand(1, 30)),
                'customer_id' => $customers->random()->id,
                'sales_id' => $sales->random()->id,
                'subtotal' => 0,
                'pajak' => 0,
                'total' => 0,
                'status' => ['draft', 'confirmed', 'paid'][array_rand(['draft', 'confirmed', 'paid'])],
            ]);

            $subtotal = 0;
            for ($j = 1; $j <= rand(2, 5); $j++) {
                $product = $products->random();
                $qty = rand(1, 10);
                $price = $product->harga_jual;
                $amount = $qty * $price;
                $subtotal += $amount;

                InvoiceDetail::create([
                    'invoice_id' => $invoice->id,
                    'barang_id' => $product->id,
                    'qty' => $qty,
                    'harga' => $price,
                    'jumlah' => $amount,
                ]);
            }

            $tax = $subtotal * 0.1;
            $total = $subtotal + $tax;

            $invoice->update([
                'subtotal' => $subtotal,
                'pajak' => $tax,
                'total' => $total,
            ]);
        }
    }

    private function seedPurchases(): void
    {
        $suppliers = MSupplier::all();
        $products = MBarang::all();

        for ($i = 1; $i <= 5; $i++) {
            $purchase = PartPenerimaan::create([
                'nomor' => 'PO-' . date('Ymd') . '-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'tanggal' => now()->subDays(rand(1, 30)),
                'supplier_id' => $suppliers->random()->id,
                'subtotal' => 0,
                'pajak' => 0,
                'total' => 0,
                'status' => ['draft', 'confirmed', 'received'][array_rand(['draft', 'confirmed', 'received'])],
            ]);

            $subtotal = 0;
            for ($j = 1; $j <= rand(2, 4); $j++) {
                $product = $products->random();
                $qty = rand(10, 50);
                $price = $product->harga_beli;
                $amount = $qty * $price;
                $subtotal += $amount;

                PartPenerimaanDetail::create([
                    'penerimaan_id' => $purchase->id,
                    'barang_id' => $product->id,
                    'qty' => $qty,
                    'harga' => $price,
                    'jumlah' => $amount,
                ]);
            }

            $tax = $subtotal * 0.1;
            $total = $subtotal + $tax;

            $purchase->update([
                'subtotal' => $subtotal,
                'pajak' => $tax,
                'total' => $total,
            ]);
        }
    }

    private function seedStock(): void
    {
        $products = MBarang::all();

        foreach ($products as $product) {
            for ($i = 1; $i <= 5; $i++) {
                KartuStok::create([
                    'tanggal' => now()->subDays(rand(1, 30)),
                    'barang_id' => $product->id,
                    'jenis' => ['masuk', 'keluar'][array_rand(['masuk', 'keluar'])],
                    'qty' => rand(1, 20),
                    'harga' => $product->harga_beli,
                    'keterangan' => "Transaksi stok {$i} - {$product->nama}",
                    'saldo' => $product->stok + rand(-10, 10),
                ]);
            }

            StokMinimum::create([
                'barang_id' => $product->id,
                'minimum' => $product->stok_minimum,
                'maksimum' => $product->stok_minimum * 5,
                'status' => $product->stok <= $product->stok_minimum ? 'low' : 'normal',
            ]);
        }
    }

    private function seedFinancial(): void
    {
        $banks = MBank::all();

        for ($i = 1; $i <= 5; $i++) {
            SaldoBank::create([
                'bank_id' => $banks->random()->id,
                'tanggal' => now()->subDays(rand(1, 30)),
                'saldo_awal' => rand(10000000, 50000000),
                'saldo_akhir' => rand(15000000, 60000000),
                'mutasi_debit' => rand(1000000, 10000000),
                'mutasi_kredit' => rand(500000, 5000000),
            ]);
        }
    }

    private function seedVouchers(): void
    {
        for ($i = 1; $i <= 5; $i++) {
            $voucher = MVoucher::create([
                'nomor' => 'VCH-' . date('Ymd') . '-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'tanggal' => now()->subDays(rand(1, 30)),
                'jenis' => ['cash_in', 'cash_out'][array_rand(['cash_in', 'cash_out'])],
                'keterangan' => "Voucher {$i}",
                'total' => rand(1000000, 10000000),
                'status' => ['draft', 'approved', 'paid'][array_rand(['draft', 'approved', 'paid'])],
            ]);

            DVoucher::create([
                'voucher_id' => $voucher->id,
                'coa_id' => MCOA::inRandomOrder()->first()->id,
                'keterangan' => "Detail voucher {$i}",
                'debit' => $voucher->jenis == 'cash_in' ? $voucher->total : 0,
                'kredit' => $voucher->jenis == 'cash_out' ? $voucher->total : 0,
            ]);
        }
    }

    private function seedClaims(): void
    {
        $customers = MCust::all();
        $products = MBarang::all();

        for ($i = 1; $i <= 5; $i++) {
            $claim = ClaimPenjualan::create([
                'nomor' => 'CLM-' . date('Ymd') . '-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'tanggal' => now()->subDays(rand(1, 30)),
                'customer_id' => $customers->random()->id,
                'total' => 0,
                'status' => ['pending', 'approved', 'rejected'][array_rand(['pending', 'approved', 'rejected'])],
                'keterangan' => "Claim penjualan {$i}",
            ]);

            $total = 0;
            for ($j = 1; $j <= rand(1, 3); $j++) {
                $product = $products->random();
                $qty = rand(1, 5);
                $price = $product->harga_jual;
                $amount = $qty * $price;
                $total += $amount;

                ClaimPenjualanDetail::create([
                    'claim_id' => $claim->id,
                    'barang_id' => $product->id,
                    'qty' => $qty,
                    'harga' => $price,
                    'jumlah' => $amount,
                    'alasan' => "Barang rusak/cacat {$j}",
                ]);
            }

            $claim->update(['total' => $total]);
        }
    }

    private function seedOpname(): void
    {
        $products = MBarang::all();

        for ($i = 1; $i <= 5; $i++) {
            $opname = Opname::create([
                'nomor' => 'OP-' . date('Ymd') . '-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'tanggal' => now()->subDays(rand(1, 30)),
                'keterangan' => "Stock opname {$i}",
                'status' => ['draft', 'approved'][array_rand(['draft', 'approved'])],
                'user_id' => User::inRandomOrder()->first()->id,
            ]);

            foreach ($products->take(3) as $product) {
                $systemStock = $product->stok;
                $physicalStock = $systemStock + rand(-5, 5);
                $selisih = $physicalStock - $systemStock;

                OpnameDetail::create([
                    'opname_id' => $opname->id,
                    'barang_id' => $product->id,
                    'stok_sistem' => $systemStock,
                    'stok_fisik' => $physicalStock,
                    'selisih' => $selisih,
                    'keterangan' => $selisih != 0 ? 'Ada selisih stok' : 'Stok sesuai',
                ]);
            }
        }
    }

    private function seedJournal(): void
    {
        $coas = MCOA::all();

        for ($i = 1; $i <= 5; $i++) {
            Journal::create([
                'nomor' => 'JRN-' . date('Ymd') . '-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'tanggal' => now()->subDays(rand(1, 30)),
                'coa_debit' => $coas->random()->id,
                'coa_kredit' => $coas->random()->id,
                'jumlah' => rand(1000000, 50000000),
                'keterangan' => "Jurnal entry {$i}",
                'referensi' => "REF-{$i}",
                'status' => ['draft', 'posted'][array_rand(['draft', 'posted'])],
            ]);
        }
    }
}