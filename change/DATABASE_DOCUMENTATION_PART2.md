# 📑 DOKUMENTASI DATABASE MAJU JAYA - BAGIAN 2
## DOKUMENTASI VIEW (24 Views)

---

### 📋 **DAFTAR ISI BAGIAN 2**
1. [View 1](#view-1)
2. [View 2](#view-2)
3. [View 3](#view-3)
4. ...

---

> **Catatan:**
> - Bagian ini berisi dokumentasi seluruh view (24 views) yang ada di database hasil migrasi.
> - Setiap view akan dijelaskan: nama, fungsi bisnis, struktur kolom, dependensi, dan contoh query.
> - Penomoran dan urutan view akan mengikuti urutan pada file migrasi/implementasi.

---

## 📖 **TEMPLATE DOKUMENTASI VIEW**

### X. **NAMA_VIEW**
**Fungsi:** Penjelasan singkat fungsi view ini dalam bisnis.

| Kolom         | Tipe Data   | Deskripsi                |
|---------------|------------|--------------------------|
| nama_kolom_1  | Tipe Data   | Penjelasan kolom         |
| nama_kolom_2  | Tipe Data   | Penjelasan kolom         |
| ...           | ...         | ...                      |

**Dependensi:**
- Tabel/view yang digunakan oleh view ini

**Contoh Query:**
```sql
SELECT * FROM nama_view WHERE ...;
```

---

## 🚀 **DAFTAR VIEW**


### 1. **v_bank**
**Fungsi:** Menampilkan daftar rekening bank beserta detail bank.

| Kolom         | Tipe Data | Deskripsi |
|---------------|----------|-----------|
| kode_divisi   | -        | Kode divisi |
| no_rekening   | -        | Nomor rekening bank |
| kode_bank     | -        | Kode bank |
| nama_bank     | -        | Nama bank |
| atas_nama     | -        | Nama pemilik rekening |
| status        | -        | Status rekening |

**Dependensi:** d_bank, m_bank

**Contoh Query:**
```sql
SELECT * FROM v_bank;
```

---

### 2. **v_barang**
**Fungsi:** Menampilkan master barang dengan detail stok dan harga.

| Kolom         | Tipe Data | Deskripsi |
|---------------|----------|-----------|
| kode_divisi   | -        | Kode divisi |
| kode_barang   | -        | Kode barang |
| nama_barang   | -        | Nama barang |
| kode_kategori | -        | Kode kategori |
| harga_list    | -        | Harga list |
| harga_jual    | -        | Harga jual |
| tgl_masuk     | -        | Tanggal masuk detail barang |
| modal         | -        | Modal barang |
| stok          | -        | Stok barang |
| satuan        | -        | Satuan |
| merk          | -        | Merk barang |
| disc1         | -        | Diskon 1 |
| disc2         | -        | Diskon 2 |
| barcode       | -        | Barcode |
| status        | -        | Status barang |
| id            | -        | ID detail barang |
| lokasi        | -        | Lokasi |

**Dependensi:** m_barang, d_barang

**Contoh Query:**
```sql
SELECT * FROM v_barang;
```

---

### 3. **v_customer_resi**
**Fungsi:** Menampilkan data resi pembayaran customer beserta detail rekening dan customer.

| Kolom         | Tipe Data | Deskripsi |
|---------------|----------|-----------|
| kode_divisi   | -        | Kode divisi |
| no_resi       | -        | Nomor resi |
| no_rekening_tujuan | -   | Nomor rekening tujuan |
| tgl_pembayaran| -        | Tanggal pembayaran |
| kode_cust     | -        | Kode customer |
| nama_cust     | -        | Nama customer |
| jumlah        | -        | Jumlah pembayaran |
| sisa_resi     | -        | Sisa resi |
| keterangan    | -        | Keterangan |
| status        | -        | Status resi |
| kode_bank     | -        | Kode bank |
| nama_bank     | -        | Nama bank |

**Dependensi:** m_resi, d_bank, m_bank, m_cust

**Contoh Query:**
```sql
SELECT * FROM v_customer_resi;
```

---

### 4. **v_cust_retur**
**Fungsi:** Menampilkan data return customer beserta nama customer.

| Kolom         | Tipe Data | Deskripsi |
|---------------|----------|-----------|
| kode_divisi   | -        | Kode divisi |
| no_retur      | -        | Nomor retur |
| tgl_retur     | -        | Tanggal retur |
| kode_cust     | -        | Kode customer |
| nama_cust     | -        | Nama customer |
| total         | -        | Total nilai retur |
| sisa_retur    | -        | Sisa retur |
| keterangan    | -        | Keterangan |
| status        | -        | Status retur |

**Dependensi:** return_sales, m_cust

**Contoh Query:**
```sql
SELECT * FROM v_cust_retur;
```

---

### 5. **v_invoice**
**Fungsi:** Menampilkan data invoice lengkap dengan detail barang, customer, sales, dan company.

| Kolom         | Tipe Data | Deskripsi |
|---------------|----------|-----------|
| no_invoice    | -        | Nomor invoice |
| tgl_faktur    | -        | Tanggal faktur |
| kode_cust     | -        | Kode customer |
| nama_cust     | -        | Nama customer |
| kode_sales    | -        | Kode sales |
| nama_sales    | -        | Nama sales |
| kode_divisi   | -        | Kode divisi |
| kode_area     | -        | Kode area customer |
| area          | -        | Nama area |
| tipe          | -        | Tipe invoice |
| jatuh_tempo   | -        | Jatuh tempo |
| grand_total   | -        | Grand total |
| sisa_invoice  | -        | Sisa invoice |
| ket           | -        | Keterangan |
| status        | -        | Status invoice |
| kode_barang   | -        | Kode barang |
| nama_barang   | -        | Nama barang |
| kode_kategori | -        | Kode kategori |
| kategori      | -        | Nama kategori |
| qty_supply    | -        | Quantity supply |
| harga_jual    | -        | Harga jual |
| jenis         | -        | Jenis item |
| diskon1       | -        | Diskon 1 |
| diskon2       | -        | Diskon 2 |
| harga_nett    | -        | Harga nett |
| status_detail | -        | Status detail |
| id            | -        | ID detail |
| merk          | -        | Merk barang |
| alamat_cust   | -        | Alamat customer |
| total         | -        | Total |
| disc          | -        | Diskon |
| pajak         | -        | Pajak |
| company_name  | -        | Nama perusahaan |
| alamat        | -        | Alamat perusahaan |
| kota          | -        | Kota perusahaan |
| an            | -        | Atas nama perusahaan |
| telp_cust     | -        | Telepon customer |
| npwp_cust     | -        | NPWP customer |
| telp          | -        | Telepon perusahaan |
| npwp          | -        | NPWP perusahaan |
| satuan        | -        | Satuan barang |
| username      | -        | Username input |
| tt            | -        | Nomor TT |

**Dependensi:** invoice, invoice_detail, m_barang, m_kategori, m_cust, m_area, m_sales, company

**Contoh Query:**
```sql
SELECT * FROM v_invoice;
```

---

### 6. **v_invoice_header**
**Fungsi:** Menampilkan header invoice beserta customer, sales, dan area.

| Kolom         | Tipe Data | Deskripsi |
|---------------|----------|-----------|
| no_invoice    | -        | Nomor invoice |
| tgl_faktur    | -        | Tanggal faktur |
| kode_cust     | -        | Kode customer |
| nama_cust     | -        | Nama customer |
| kode_area     | -        | Kode area |
| area          | -        | Nama area |
| kode_sales    | -        | Kode sales |
| nama_sales    | -        | Nama sales |
| tipe          | -        | Tipe invoice |
| jatuh_tempo   | -        | Jatuh tempo |
| grand_total   | -        | Grand total |
| sisa_invoice  | -        | Sisa invoice |
| ket           | -        | Keterangan |
| status        | -        | Status invoice |
| kode_divisi   | -        | Kode divisi |
| total         | -        | Total |
| disc          | -        | Diskon |
| pajak         | -        | Pajak |
| no_npwp       | -        | NPWP customer |
| nama_pajak    | -        | Nama pajak customer |
| alamat_pajak  | -        | Alamat pajak customer |
| username      | -        | Username input |
| tt            | -        | Nomor TT |

**Dependensi:** invoice, m_sales, m_cust, m_area

**Contoh Query:**
```sql
SELECT * FROM v_invoice_header;
```

---

### 7. **v_journal**
**Fungsi:** Menampilkan jurnal umum beserta nama akun.

| Kolom         | Tipe Data | Deskripsi |
|---------------|----------|-----------|
| id            | -        | ID jurnal |
| tanggal       | -        | Tanggal transaksi |
| transaksi     | -        | Nama transaksi |
| kode_coa      | -        | Kode akun |
| nama_coa      | -        | Nama akun |
| keterangan    | -        | Keterangan |
| debet         | -        | Debet |
| credit        | -        | Kredit |

**Dependensi:** journal, m_coa

**Contoh Query:**
```sql
SELECT * FROM v_journal;
```

---

### 8. **v_kartu_stok**
**Fungsi:** Menampilkan kartu stok barang beserta detail barang.

| Kolom         | Tipe Data | Deskripsi |
|---------------|----------|-----------|
| urut          | -        | Urutan |
| kode_divisi   | -        | Kode divisi |
| kode_barang   | -        | Kode barang |
| nama_barang   | -        | Nama barang |
| kode_kategori | -        | Kode kategori |
| no_ref        | -        | Nomor referensi |
| tgl_proses    | -        | Tanggal proses |
| tipe          | -        | Tipe transaksi |
| increase      | -        | Penambahan stok |
| decrease      | -        | Pengurangan stok |
| harga_debet   | -        | Harga debet |
| harga_kredit  | -        | Harga kredit |
| qty           | -        | Quantity |
| hpp           | -        | Harga pokok penjualan |

**Dependensi:** kartu_stok, m_barang

**Contoh Query:**
```sql
SELECT * FROM v_kartu_stok;
```

---

### 9. **v_part_penerimaan**
**Fungsi:** Menampilkan detail penerimaan barang beserta supplier dan barang.

| Kolom         | Tipe Data | Deskripsi |
|---------------|----------|-----------|
| kode_divisi   | -        | Kode divisi |
| no_penerimaan | -        | Nomor penerimaan |
| tgl_penerimaan| -        | Tanggal penerimaan |
| kode_supplier | -        | Kode supplier |
| nama_supplier | -        | Nama supplier |
| jatuh_tempo   | -        | Jatuh tempo |
| no_faktur     | -        | Nomor faktur |
| total         | -        | Total penerimaan |
| discount      | -        | Diskon |
| pajak         | -        | Pajak |
| grand_total   | -        | Grand total |
| status        | -        | Status penerimaan |
| kode_barang   | -        | Kode barang |
| qty_supply    | -        | Quantity supply |
| harga         | -        | Harga barang |
| diskon1       | -        | Diskon 1 |
| diskon2       | -        | Diskon 2 |
| harga_nett    | -        | Harga nett |
| nama_barang   | -        | Nama barang |

**Dependensi:** part_penerimaan, part_penerimaan_detail, m_supplier, m_barang

**Contoh Query:**
```sql
SELECT * FROM v_part_penerimaan;
```

---

### 10. **v_part_penerimaan_header**
**Fungsi:** Menampilkan header penerimaan barang beserta supplier.

| Kolom         | Tipe Data | Deskripsi |
|---------------|----------|-----------|
| kode_divisi   | -        | Kode divisi |
| no_penerimaan | -        | Nomor penerimaan |
| tgl_penerimaan| -        | Tanggal penerimaan |
| kode_supplier | -        | Kode supplier |
| nama_supplier | -        | Nama supplier |
| jatuh_tempo   | -        | Jatuh tempo |
| no_faktur     | -        | Nomor faktur |
| total         | -        | Total penerimaan |
| discount      | -        | Diskon |
| pajak         | -        | Pajak |
| grand_total   | -        | Grand total |
| status        | -        | Status penerimaan |

**Dependensi:** part_penerimaan, m_supplier

**Contoh Query:**
```sql
SELECT * FROM v_part_penerimaan_header;
```

---

### 11. **v_penerimaan_finance**
**Fungsi:** Menampilkan data penerimaan keuangan beserta nama customer.

| Kolom         | Tipe Data | Deskripsi |
|---------------|----------|-----------|
| kode_divisi   | -        | Kode divisi |
| no_penerimaan | -        | Nomor penerimaan |
| tgl_penerimaan| -        | Tanggal penerimaan |
| tipe          | -        | Tipe penerimaan |
| no_ref        | -        | Nomor referensi |
| tgl_ref       | -        | Tanggal referensi |
| tgl_pencairan | -        | Tanggal pencairan |
| bank_ref      | -        | Referensi bank |
| no_rek_tujuan | -        | Rekening tujuan |
| kode_cust     | -        | Kode customer |
| jumlah        | -        | Jumlah penerimaan |
| status        | -        | Status penerimaan |
| nama_cust     | -        | Nama customer |

**Dependensi:** penerimaan_finance, m_cust

**Contoh Query:**
```sql
SELECT * FROM v_penerimaan_finance;
```

---

### 12. **v_penerimaan_finance_detail**
**Fungsi:** Menampilkan detail penerimaan keuangan beserta invoice, customer, dan sales.

| Kolom         | Tipe Data | Deskripsi |
|---------------|----------|-----------|
| kode_divisi   | -        | Kode divisi |
| no_penerimaan | -        | Nomor penerimaan |
| tgl_penerimaan| -        | Tanggal penerimaan |
| tipe          | -        | Tipe penerimaan |
| no_ref        | -        | Nomor referensi |
| tgl_ref       | -        | Tanggal referensi |
| tgl_pencairan | -        | Tanggal pencairan |
| bank_ref      | -        | Referensi bank |
| no_rek_tujuan | -        | Rekening tujuan |
| kode_cust     | -        | Kode customer |
| jumlah        | -        | Jumlah penerimaan |
| status        | -        | Status penerimaan |
| no_invoice    | -        | Nomor invoice |
| jumlah_invoice| -        | Jumlah invoice |
| jumlah_bayar  | -        | Jumlah dibayar |
| jumlah_dispensasi | -    | Jumlah dispensasi |
| status_detail | -        | Status detail pembayaran |
| id            | -        | ID detail |
| sisa_invoice  | -        | Sisa invoice |
| sisa_bayar    | -        | Sisa bayar |
| nama_cust     | -        | Nama customer |
| kode_sales    | -        | Kode sales |
| nama_sales    | -        | Nama sales |
| no_voucher    | -        | Nomor voucher |

**Dependensi:** penerimaan_finance, penerimaan_finance_detail, m_cust, invoice, m_sales

**Contoh Query:**
```sql
SELECT * FROM v_penerimaan_finance_detail;
```

---

### 13. **v_return_sales_detail**
**Fungsi:** Menampilkan detail return penjualan beserta customer, invoice, sales, dan barang.

| Kolom         | Tipe Data | Deskripsi |
|---------------|----------|-----------|
| kode_divisi   | -        | Kode divisi |
| no_retur      | -        | Nomor retur |
| tgl_retur     | -        | Tanggal retur |
| kode_cust     | -        | Kode customer |
| nama_cust     | -        | Nama customer |
| alamat_cust   | -        | Alamat customer |
| total         | -        | Total retur |
| no_invoice    | -        | Nomor invoice |
| tgl_faktur    | -        | Tanggal faktur |
| kode_sales    | -        | Kode sales |
| nama_sales    | -        | Nama sales |
| kode_barang   | -        | Kode barang |
| nama_barang   | -        | Nama barang |
| satuan        | -        | Satuan barang |
| merk          | -        | Merk barang |
| qty_retur     | -        | Quantity retur |
| harga_nett    | -        | Harga nett |
| telp          | -        | Telepon customer |
| status        | -        | Status retur |
| tt            | -        | Nomor TT |

**Dependensi:** return_sales, return_sales_detail, m_cust, invoice, m_sales, m_barang

**Contoh Query:**
```sql
SELECT * FROM v_return_sales_detail;
```

---

### 14. **v_trans**
**Fungsi:** Menampilkan detail transaksi beserta nama akun dan transaksi.

| Kolom         | Tipe Data | Deskripsi |
|---------------|----------|-----------|
| kode_trans    | -        | Kode transaksi |
| kode_coa      | -        | Kode akun |
| saldo_normal  | -        | Saldo normal |
| transaksi     | -        | Nama transaksi |
| nama_coa      | -        | Nama akun |

**Dependensi:** d_trans, m_trans, m_coa

**Contoh Query:**
```sql
SELECT * FROM v_trans;
```

---

### 15. **v_tt**
**Fungsi:** Menampilkan data transaksi tertentu beserta nama customer.

| Kolom         | Tipe Data | Deskripsi |
|---------------|----------|-----------|
| kode_divisi   | -        | Kode divisi |
| no_tt         | -        | Nomor TT |
| tanggal       | -        | Tanggal transaksi |
| kode_cust     | -        | Kode customer |
| nama_cust     | -        | Nama customer |
| keterangan    | -        | Keterangan transaksi |

**Dependensi:** m_tt, m_cust

**Contoh Query:**
```sql
SELECT * FROM v_tt;
```

---

### 16. **v_tt_invoice**
**Fungsi:** Menampilkan data transaksi tertentu beserta invoice dan sales.

| Kolom         | Tipe Data | Deskripsi |
|---------------|----------|-----------|
| kode_divisi   | -        | Kode divisi |
| no_tt         | -        | Nomor TT |
| tanggal       | -        | Tanggal transaksi |
| kode_cust     | -        | Kode customer |
| nama_cust     | -        | Nama customer |
| keterangan    | -        | Keterangan transaksi |
| no_ref        | -        | Nomor referensi (invoice) |
| tgl_faktur    | -        | Tanggal faktur |
| nama_sales    | -        | Nama sales |
| grand_total   | -        | Grand total invoice |
| sisa_invoice  | -        | Sisa invoice |

**Dependensi:** m_tt, d_tt, m_cust, invoice, m_sales

**Contoh Query:**
```sql
SELECT * FROM v_tt_invoice;
```

---

### 17. **v_tt_retur**
**Fungsi:** Menampilkan data transaksi tertentu beserta detail retur dan barang.

| Kolom         | Tipe Data | Deskripsi |
|---------------|----------|-----------|
| kode_divisi   | -        | Kode divisi |
| no_tt         | -        | Nomor TT |
| tanggal       | -        | Tanggal transaksi |
| kode_cust     | -        | Kode customer |
| nama_cust     | -        | Nama customer |
| no_ref        | -        | Nomor referensi (retur) |
| tgl_retur     | -        | Tanggal retur |
| kode_barang   | -        | Kode barang |
| nama_barang   | -        | Nama barang |
| qty_retur     | -        | Quantity retur |
| harga_nett    | -        | Harga nett |
| merk          | -        | Merk barang |
| status        | -        | Status retur |

**Dependensi:** m_tt, m_cust, d_tt, return_sales, return_sales_detail, m_barang

**Contoh Query:**
```sql
SELECT * FROM v_tt_retur;
```

---

### 18. **v_voucher**
**Fungsi:** Menampilkan data komisi sales beserta nama sales.

| Kolom         | Tipe Data | Deskripsi |
|---------------|----------|-----------|
| kode_divisi   | -        | Kode divisi |
| no_voucher    | -        | Nomor voucher |
| tanggal       | -        | Tanggal voucher |
| kode_sales    | -        | Kode sales |
| nama_sales    | -        | Nama sales |
| total_omzet   | -        | Total omzet |
| komisi        | -        | Persentase komisi |
| jumlah_komisi | -        | Jumlah komisi |

**Dependensi:** m_voucher, m_sales

**Contoh Query:**
```sql
SELECT * FROM v_voucher;
```

---

### 19. **v_stok_summary**
**Fungsi:** Monitoring summary stok barang dan status stok.

| Kolom         | Tipe Data | Deskripsi |
|---------------|----------|-----------|
| kode_divisi   | -        | Kode divisi |
| kode_barang   | -        | Kode barang |
| nama_barang   | -        | Nama barang |
| satuan        | -        | Satuan barang |
| total_stok    | -        | Total stok |
| stok_min      | -        | Stok minimum |
| lokasi        | -        | Lokasi |
| status_stok   | -        | Status stok (CRITICAL/LOW/NORMAL) |

**Dependensi:** m_barang, d_barang

**Contoh Query:**
```sql
SELECT * FROM v_stok_summary;
```

---

### 20. **v_financial_report**
**Fungsi:** Laporan keuangan dengan saldo dan penyesuaian saldo.

| Kolom         | Tipe Data | Deskripsi |
|---------------|----------|-----------|
| tanggal       | -        | Tanggal transaksi |
| kode_coa      | -        | Kode akun |
| nama_coa      | -        | Nama akun |
| saldo_normal  | -        | Saldo normal |
| transaksi     | -        | Nama transaksi |
| keterangan    | -        | Keterangan |
| debet         | -        | Debet |
| credit        | -        | Kredit |
| balance       | -        | Saldo |
| adjusted_balance | -     | Saldo penyesuaian |

**Dependensi:** journal, m_coa

**Contoh Query:**
```sql
SELECT * FROM v_financial_report;
```

---

### 21. **v_aging_report**
**Fungsi:** Laporan aging piutang berdasarkan jatuh tempo invoice.

| Kolom         | Tipe Data | Deskripsi |
|---------------|----------|-----------|
| kode_divisi   | -        | Kode divisi |
| no_invoice    | -        | Nomor invoice |
| tgl_faktur    | -        | Tanggal faktur |
| jatuh_tempo   | -        | Jatuh tempo |
| kode_cust     | -        | Kode customer |
| nama_cust     | -        | Nama customer |
| grand_total   | -        | Grand total |
| sisa_invoice  | -        | Sisa invoice |
| status        | -        | Status invoice |
| aging_category| -        | Kategori aging |
| days_overdue  | -        | Hari lewat jatuh tempo |

**Dependensi:** invoice, m_cust

**Contoh Query:**
```sql
SELECT * FROM v_aging_report;
```

---

### 22. **v_sales_summary**
**Fungsi:** Summary penjualan per sales per bulan dan tahun.

| Kolom         | Tipe Data | Deskripsi |
|---------------|----------|-----------|
| kode_divisi   | -        | Kode divisi |
| kode_sales    | -        | Kode sales |
| nama_sales    | -        | Nama sales |
| nama_area     | -        | Nama area |
| total_transaksi | -      | Total transaksi |
| total_penjualan | -      | Total penjualan |
| rata_rata_per_transaksi | - | Rata-rata per transaksi |
| total_piutang | -        | Total piutang |
| bulan         | -        | Bulan |
| tahun         | -        | Tahun |

**Dependensi:** invoice, m_sales, m_area

**Contoh Query:**
```sql
SELECT * FROM v_sales_summary;
```

---

### 23. **v_return_summary**
**Fungsi:** Summary return/retur per customer dan kategori.

| Kolom         | Tipe Data | Deskripsi |
|---------------|----------|-----------|
| kode_divisi   | -        | Kode divisi |
| no_retur      | -        | Nomor retur |
| tgl_retur     | -        | Tanggal retur |
| kode_cust     | -        | Kode customer |
| nama_cust     | -        | Nama customer |
| status        | -        | Status retur |
| total_item    | -        | Total item retur |
| total_nilai_retur | -    | Total nilai retur |
| kategori_retur| -        | Kategori retur |

**Dependensi:** return_sales, m_cust, return_sales_detail

**Contoh Query:**
```sql
SELECT * FROM v_return_summary;
```

---

### 24. **v_dashboard_kpi**
**Fungsi:** Menampilkan KPI dashboard untuk overview bisnis.

| Kolom         | Tipe Data | Deskripsi |
|---------------|----------|-----------|
| total_invoice | -        | Total invoice aktif |
| total_penjualan | -      | Total penjualan |
| total_piutang | -        | Total piutang |
| total_customer_aktif | - | Total customer aktif |
| total_produk_aktif | -  | Total produk aktif |
| produk_stok_kritis | -  | Produk dengan stok kritis |
| rata_rata_penjualan_bulanan | - | Rata-rata penjualan bulanan |

**Dependensi:** invoice, m_cust, m_barang, v_stok_summary

**Contoh Query:**
```sql
SELECT * FROM v_dashboard_kpi;
```

---

---

*Generated: 3 September 2025*
