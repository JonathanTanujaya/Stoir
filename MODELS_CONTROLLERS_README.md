# Models dan Controllers ERP System

## Overview
Dokumentasi ini menjelaskan semua Model dan Controller yang telah dibuat untuk sistem ERP berdasarkan database PostgreSQL yang ada.

## Core Models (22 Models)

### 1. Area.php
- **Primary Key**: `kode_area`
- **Relationships**: 
  - belongsTo Divisi
  - hasMany Customer
- **Purpose**: Manajemen area geografis

### 2. Bank.php
- **Primary Key**: `['kode_divisi', 'kode_bank']`
- **Relationships**: 
  - belongsTo Divisi
  - hasMany DetailBank, SaldoBank
- **Purpose**: Manajemen bank dan rekening

### 3. Barang.php
- **Primary Key**: `kode_barang`
- **Relationships**: 
  - belongsTo Kategori
  - hasMany DetailBarang, InvoiceDetail, KartuStok, StokMinimum
- **Purpose**: Master data barang/produk

### 4. Coa.php
- **Primary Key**: `['kode_divisi', 'kode_coa']`
- **Relationships**: 
  - belongsTo Divisi
  - hasMany Journal
- **Purpose**: Chart of Accounts untuk akuntansi

### 5. Company.php
- **Primary Key**: `kode_divisi`
- **Relationships**: 
  - belongsTo Divisi
- **Purpose**: Data perusahaan per divisi

### 6. Customer.php
- **Primary Key**: `['kode_divisi', 'kode_cust']`
- **Relationships**: 
  - belongsTo Divisi, Area, Sales
  - hasMany Invoice
- **Purpose**: Master data pelanggan

### 7. DetailBank.php
- **Primary Key**: `['kode_divisi', 'kode_bank', 'tgl_transaksi', 'no_bukti']`
- **Relationships**: 
  - belongsTo Bank
- **Purpose**: Detail transaksi bank

### 8. DetailBarang.php
- **Primary Key**: `['kode_divisi', 'kode_barang', 'no_bukti']`
- **Relationships**: 
  - belongsTo Barang
- **Purpose**: Detail transaksi barang

### 9. DetailTransaction.php
- **Primary Key**: `['kode_divisi', 'no_bukti', 'kode_coa']`
- **Relationships**: 
  - belongsTo Divisi, Coa
- **Purpose**: Detail transaksi keuangan

### 10. Divisi.php
- **Primary Key**: `kode_divisi`
- **Relationships**: 
  - hasMany Bank, Customer, Supplier, dll.
- **Purpose**: Master divisi/cabang

### 11. Invoice.php
- **Primary Key**: `['kode_divisi', 'no_invoice']`
- **Relationships**: 
  - belongsTo Divisi, Customer, Sales
  - hasMany InvoiceDetail
- **Purpose**: Header invoice penjualan

### 12. InvoiceDetail.php
- **Primary Key**: `['kode_divisi', 'no_invoice', 'kode_barang']`
- **Relationships**: 
  - belongsTo Invoice, Barang
- **Purpose**: Detail item invoice

### 13. Journal.php
- **Primary Key**: `['kode_divisi', 'no_jurnal', 'kode_coa']`
- **Relationships**: 
  - belongsTo Divisi, Coa
- **Purpose**: Jurnal akuntansi

### 14. KartuStok.php
- **Primary Key**: `['kode_divisi', 'kode_barang']`
- **Relationships**: 
  - belongsTo Divisi, Barang
- **Purpose**: Kartu stok barang

### 15. Kategori.php
- **Primary Key**: `kode_kategori`
- **Relationships**: 
  - hasMany Barang
- **Purpose**: Kategori barang

### 16. PartPenerimaan.php
- **Primary Key**: `['kode_divisi', 'no_part_penerimaan']`
- **Relationships**: 
  - belongsTo Divisi, Supplier
  - hasMany PartPenerimaanDetail
- **Purpose**: Header penerimaan barang

### 17. PartPenerimaanDetail.php
- **Primary Key**: `['kode_divisi', 'no_part_penerimaan', 'kode_barang']`
- **Relationships**: 
  - belongsTo PartPenerimaan, Barang
- **Purpose**: Detail penerimaan barang

### 18. Sales.php
- **Primary Key**: `['kode_divisi', 'kode_sales']`
- **Relationships**: 
  - belongsTo Divisi
  - hasMany Customer, Invoice
- **Purpose**: Master data sales/marketing

### 19. Supplier.php
- **Primary Key**: `['kode_divisi', 'kode_supplier']`
- **Relationships**: 
  - belongsTo Divisi
  - hasMany PartPenerimaan
- **Purpose**: Master data supplier

### 20. TransactionType.php
- **Primary Key**: `kode_tt`
- **Relationships**: 
  - hasMany various transaction tables
- **Purpose**: Jenis transaksi

### 21. User.php
- **Primary Key**: `id`
- **Relationships**: 
  - belongsTo Divisi
- **Purpose**: User management

## Additional Models (15 Models)

### 22. DPaket.php
- **Primary Key**: `['kode_divisi', 'nomor']`
- **Purpose**: Detail paket/bundling

### 23. DTt.php
- **Primary Key**: `['kode_divisi', 'nomor']`
- **Purpose**: Detail tipe transaksi

### 24. DVoucher.php
- **Primary Key**: `['kode_divisi', 'nomor']`
- **Purpose**: Detail voucher

### 25. MDokumen.php
- **Primary Key**: `['kode_divisi', 'nomor']`
- **Purpose**: Master dokumen

### 26. MResi.php
- **Primary Key**: `['kode_divisi', 'nomor']`
- **Purpose**: Master resi pengiriman

### 27. MTt.php
- **Primary Key**: `['kode_divisi', 'nomor']`
- **Purpose**: Master tipe transaksi

### 28. MVoucher.php
- **Primary Key**: `['kode_divisi', 'nomor']`
- **Purpose**: Master voucher

### 29. PenerimaanFinance.php
- **Primary Key**: `['kode_divisi', 'no_penerimaan_finance']`
- **Purpose**: Header penerimaan finance

### 30. PenerimaanFinanceDetail.php
- **Primary Key**: `['kode_divisi', 'no_penerimaan_finance', 'kode_coa']`
- **Purpose**: Detail penerimaan finance

### 31. ReturPenerimaan.php
- **Primary Key**: `['kode_divisi', 'no_retur_penerimaan']`
- **Purpose**: Header retur penerimaan

### 32. ReturPenerimaanDetail.php
- **Primary Key**: `['kode_divisi', 'no_retur_penerimaan', 'kode_barang']`
- **Purpose**: Detail retur penerimaan

### 33. ReturnSales.php
- **Primary Key**: `['kode_divisi', 'no_return_sales']`
- **Purpose**: Header return sales

### 34. ReturnSalesDetail.php
- **Primary Key**: `['kode_divisi', 'no_return_sales', 'kode_barang']`
- **Purpose**: Detail return sales

### 35. SaldoBank.php
- **Primary Key**: `['kode_divisi', 'kode_bank', 'tanggal']`
- **Purpose**: Saldo bank harian

### 36. StokMinimum.php
- **Primary Key**: `['kode_divisi', 'kode_barang']`
- **Purpose**: Setting minimum stok

## Controllers (24+ Controllers)

### Core Controllers
1. **AreaController** - CRUD area
2. **BankController** - CRUD bank
3. **BarangController** - CRUD barang dengan search
4. **CustomerController** - CRUD customer
5. **DivisiController** - CRUD divisi
6. **InvoiceController** - CRUD invoice dengan detail
7. **SalesController** - CRUD sales
8. **SupplierController** - CRUD supplier
9. **ReportController** - Generate laporan
10. **ProcedureController** - Execute stored procedures

### Additional Controllers
11. **DPaketController** - CRUD paket
12. **DTtController** - CRUD detail TT
13. **DVoucherController** - CRUD detail voucher
14. **MDokumenController** - CRUD master dokumen
15. **MResiController** - CRUD master resi
16. **MTtController** - CRUD master TT
17. **MVoucherController** - CRUD master voucher
18. **PenerimaanFinanceController** - CRUD penerimaan finance
19. **PenerimaanFinanceDetailController** - CRUD detail penerimaan finance
20. **ReturPenerimaanController** - CRUD retur penerimaan
21. **ReturPenerimaanDetailController** - CRUD detail retur penerimaan
22. **ReturnSalesController** - CRUD return sales
23. **ReturnSalesDetailController** - CRUD detail return sales
24. **SaldoBankController** - CRUD saldo bank dengan fungsi khusus
25. **StokMinimumController** - CRUD stok minimum dengan check low stock

## API Routes
Semua controller memiliki route API yang terstruktur dengan pola:
- `GET /api/divisi/{kodeDivisi}/resource` - List all
- `POST /api/divisi/{kodeDivisi}/resource` - Create new
- `GET /api/divisi/{kodeDivisi}/resource/{id}` - Show specific
- `PUT /api/divisi/{kodeDivisi}/resource/{id}` - Update
- `DELETE /api/divisi/{kodeDivisi}/resource/{id}` - Delete

### Special Routes
- **Reports**: `/api/reports/*`
- **Procedures**: `/api/procedures/*`
- **Bank Balance**: `/api/divisi/{kodeDivisi}/saldo-banks/bank/{kodeBank}`
- **Low Stock Check**: `/api/divisi/{kodeDivisi}/stok-minimums/check/low-stock`

## Features
- Multi-tenant architecture (scoped by `kode_divisi`)
- Composite primary keys untuk relational integrity
- Full CRUD operations dengan validation
- Relationship handling dengan Eloquent
- JSON API responses
- Error handling dan validation rules
- Special business logic untuk inventory dan finance
- **Complete foreign key constraints** sesuai database schema

## Foreign Key Constraints
Sistem ini telah dilengkapi dengan foreign key constraints lengkap berdasarkan file `add_foreign_keys.sql`:

### Core Table Constraints:
- **m_bank** → m_divisi
- **d_bank** → m_divisi, m_bank
- **m_area** → m_divisi
- **m_sales** → m_divisi, m_area
- **m_cust** → m_divisi, m_area
- **m_kategori** → m_divisi
- **m_barang** → m_divisi, m_kategori
- **d_barang** → m_divisi, m_barang
- **m_supplier** → m_divisi
- **invoice** → m_divisi, m_cust, m_sales
- **invoice_detail** → invoice, m_barang
- **kartu_stok** → m_divisi, m_barang
- **part_penerimaan** → m_divisi, m_supplier
- **part_penerimaan_detail** → part_penerimaan, m_barang

### Additional Table Constraints:
- **m_tt** → m_divisi, m_cust
- **d_tt** → m_tt
- **m_voucher** → m_divisi, m_sales
- **d_voucher** → m_voucher
- **saldo_bank** → m_divisi, d_bank
- **return_sales** → m_divisi, m_cust
- **return_sales_detail** → return_sales, invoice, m_barang
- **retur_penerimaan** → m_divisi, m_supplier
- **retur_penerimaan_detail** → retur_penerimaan, part_penerimaan, m_barang
- **m_resi** → m_divisi, d_bank, m_cust
- **penerimaan_finance** → m_divisi, d_bank, m_cust
- **penerimaan_finance_detail** → penerimaan_finance, invoice
- **m_dokumen** → m_divisi
- **d_paket** → m_divisi, m_kategori
- **stok_minimum** → m_divisi, m_barang

## Database Migrations
Disediakan migration files untuk:
1. **Core foreign keys**: `2024_12_07_000048_add_foreign_keys_core_tables.php`
2. **Additional foreign keys**: `2024_12_07_000049_add_foreign_keys_additional_tables.php`
3. **Stok minimum table**: `2024_12_07_000047_create_stok_minimum_table.php`

## Database Views dan Stored Procedures
Sistem ini juga mendukung:
- 24 database views untuk reporting
- 14 stored procedures untuk business logic
- Integration dengan PostgreSQL native functions

## Frontend Integration
React components tersedia untuk:
- Dashboard management
- CRUD operations
- Reports viewing
- Procedure execution
