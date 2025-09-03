# 📑 DOKUMENTASI DATABASE MAJU JAYA - BAGIAN 3
## DOKUMENTASI PROCEDURE/FUNCTION (14 Procedures)

---

### 📋 **DAFTAR ISI BAGIAN 3**
1. [sp_invoice](#1-sp_invoice)
2. [sp_journal_invoice](#2-sp_journal_invoice)
3. [sp_journal_retur_sales](#3-sp_journal_retur_sales)
4. [sp_master_resi](#4-sp_master_resi)
5. [sp_merge_barang](#5-sp_merge_barang)
6. [sp_opname](#6-sp_opname)
7. [sp_part_penerimaan](#7-sp_part_penerimaan)
8. [sp_pembatalan_invoice](#8-sp_pembatalan_invoice)
9. [sp_rekalkulasi](#9-sp_rekalkulasi)
10. [sp_retur_sales](#10-sp_retur_sales)
11. [sp_set_nomor](#11-sp_set_nomor)
12. [sp_tambah_saldo](#12-sp_tambah_saldo)
13. [sp_tanda_terima](#13-sp_tanda_terima)
14. [sp_voucher](#14-sp_voucher)

---

> **Catatan:**
> - Bagian ini berisi dokumentasi seluruh procedure/function (14) yang ada di database hasil migrasi.
> - Setiap procedure dijelaskan: nama, fungsi bisnis, parameter, dependensi, dan ringkasan logika utama.
> - Penomoran dan urutan procedure mengikuti urutan pada file uptProcedure.txt.

---

## 📖 **TEMPLATE DOKUMENTASI PROCEDURE**

### X. **NAMA_PROCEDURE**
**Fungsi:** Penjelasan singkat fungsi procedure ini dalam bisnis.

**Parameter:**
| Nama Parameter | Tipe Data | Keterangan |
|----------------|-----------|------------|
| ...            | ...       | ...        |

**Dependensi:**
- Tabel/procedure yang digunakan oleh procedure ini

**Ringkasan Logika:**
- ...

---

## 🚀 **DAFTAR PROCEDURE**

> Silakan isi setiap procedure sesuai template di atas.

### 1. **sp_invoice**
**Fungsi:** Membuat invoice baru beserta detailnya, mengurangi stok barang, dan mencatat pergerakan stok ke kartu_stok.

**Parameter:**
| Nama Parameter   | Tipe Data      | Keterangan                       |
|------------------|---------------|-----------------------------------|
| p_kode_divisi    | VARCHAR(5)    | Kode divisi                      |
| p_no_invoice     | VARCHAR(15)   | Nomor invoice                    |
| p_kode_cust      | VARCHAR(5)    | Kode customer                    |
| p_kode_sales     | VARCHAR(5)    | Kode sales                       |
| p_tipe           | CHAR(1)       | Tipe invoice                     |
| p_total          | NUMERIC(15,2) | Total sebelum diskon/pajak        |
| p_disc           | NUMERIC(5,2)  | Diskon                           |
| p_pajak          | NUMERIC(5,2)  | Pajak                            |
| p_grand_total    | NUMERIC(15,2) | Grand total                      |
| p_ket            | TEXT          | Keterangan                       |
| p_kode_barang    | VARCHAR(50)   | Kode barang                      |
| p_qty_supply     | INT           | Jumlah barang yang dijual        |
| p_harga_jual     | NUMERIC(15,2) | Harga jual per barang            |
| p_diskon1        | NUMERIC(5,2)  | Diskon 1 per barang              |
| p_diskon2        | NUMERIC(5,2)  | Diskon 2 per barang              |
| p_harga_nett     | NUMERIC(15,2) | Harga nett per barang            |
| p_user           | VARCHAR(50)   | User input                       |

**Dependensi:**
- invoice, invoice_detail, d_barang, m_cust, kartu_stok

**Ringkasan Logika:**
- Jika invoice belum ada, insert ke tabel invoice (ambil jatuh tempo dari m_cust)
- Insert detail barang ke invoice_detail
- Kurangi stok barang di d_barang (FIFO, pakai cursor)
- Catat pergerakan stok ke kartu_stok

---

### 2. **sp_journal_invoice**
**Fungsi:** Membuat jurnal otomatis untuk transaksi penjualan berdasarkan invoice.

**Parameter:**
| Nama Parameter   | Tipe Data      | Keterangan                       |
|------------------|---------------|-----------------------------------|
| p_no_invoice     | VARCHAR(15)   | Nomor invoice                    |

**Dependensi:**
- invoice, kartu_stok, journal

**Ringkasan Logika:**
- Ambil nilai grand_total, total dari invoice
- Hitung HPP dari kartu_stok
- Insert jurnal: debit piutang, kredit pendapatan, kredit pajak, debit HPP, kredit persediaan, debit & kredit insentif

---

### 3. **sp_journal_retur_sales**
**Fungsi:** Membuat jurnal otomatis untuk transaksi retur penjualan.

**Parameter:**
| Nama Parameter   | Tipe Data      | Keterangan                       |
|------------------|---------------|-----------------------------------|
| p_no_retur       | VARCHAR(15)   | Nomor retur                      |

**Dependensi:**
- return_sales, kartu_stok, journal

**Ringkasan Logika:**
- Ambil nilai total retur dari return_sales
- Hitung HPP dari kartu_stok
- Insert jurnal reversal: debit pendapatan, debit pajak, kredit piutang, debit persediaan, kredit HPP, debit & kredit insentif

---

### 4. **sp_master_resi**
**Fungsi:** Membuat data master resi pembayaran customer dan update saldo bank.

**Parameter:**
| Nama Parameter         | Tipe Data      | Keterangan                       |
|------------------------|---------------|-----------------------------------|
| p_kode_divisi          | VARCHAR(5)    | Kode divisi                      |
| p_no_resi              | VARCHAR(15)   | Nomor resi                       |
| p_no_rekening_tujuan   | VARCHAR(50)   | Nomor rekening tujuan            |
| p_tgl_bayar            | DATE          | Tanggal pembayaran               |
| p_kode_cust            | VARCHAR(50)   | Kode customer                    |
| p_keterangan           | TEXT          | Keterangan                       |
| p_nominal              | NUMERIC(15,2) | Nominal pembayaran               |

**Dependensi:**
- m_resi, d_bank, saldo_bank

**Ringkasan Logika:**
- Insert data ke m_resi
- Update saldo bank di d_bank dan saldo_bank

---

### 5. **sp_merge_barang**
**Fungsi:** Menggabungkan stok barang (merge) dan update stok serta kartu_stok.

**Parameter:**
| Nama Parameter         | Tipe Data      | Keterangan                       |
|------------------------|---------------|-----------------------------------|
| p_kode_divisi          | VARCHAR(4)    | Kode divisi                      |
| p_no_create            | VARCHAR(15)   | Nomor merge                      |
| p_kode_barang_create   | VARCHAR(30)   | Kode barang hasil merge          |
| p_qty_create           | INT           | Jumlah barang hasil merge        |
| p_modal_create         | NUMERIC(15,2) | Modal barang hasil merge         |
| p_biaya                | NUMERIC(15,2) | Biaya tambahan                   |
| p_kode_barang_merge    | VARCHAR(30)   | Kode barang sumber merge         |
| p_qty_merge            | INT           | Jumlah barang sumber merge       |
| p_modal_merge          | NUMERIC(15,2) | Modal barang sumber merge        |
| p_id                   | BIGINT        | ID detail barang sumber          |

**Dependensi:**
- merge_barang, d_barang, kartu_stok, merge_barang_detail

**Ringkasan Logika:**
- Insert ke merge_barang jika belum ada
- Update/insert stok barang hasil merge di d_barang
- Insert kartu_stok untuk barang hasil dan sumber merge
- Update stok barang sumber

---

### 6. **sp_opname**
**Fungsi:** Melakukan stok opname (penyesuaian stok fisik) dan update kartu_stok.

**Parameter:**
| Nama Parameter         | Tipe Data      | Keterangan                       |
|------------------------|---------------|-----------------------------------|
| p_kode_divisi          | VARCHAR(4)    | Kode divisi                      |
| p_no_opname            | VARCHAR(15)   | Nomor opname                     |
| p_total                | NUMERIC(15,2) | Total nilai opname               |
| p_kode_barang          | VARCHAR(30)   | Kode barang                      |
| p_qty                  | INT           | Jumlah penyesuaian               |
| p_modal                | NUMERIC(15,2) | Modal barang                     |
| p_id                   | BIGINT        | ID detail barang                 |

**Dependensi:**
- opname, opname_detail, d_barang, kartu_stok

**Ringkasan Logika:**
- Insert ke opname jika belum ada
- Insert opname_detail
- Update stok barang di d_barang
- Insert kartu_stok sesuai penyesuaian (plus/minus)

---

### 7. **sp_part_penerimaan**
**Fungsi:** Mencatat penerimaan barang (pembelian), update stok, dan jurnal pembelian.

**Parameter:**
| Nama Parameter         | Tipe Data      | Keterangan                       |
|------------------------|---------------|-----------------------------------|
| p_kode_divisi          | VARCHAR(4)    | Kode divisi                      |
| p_no_penerimaan        | VARCHAR(15)   | Nomor penerimaan                 |
| p_tgl_penerimaan       | DATE          | Tanggal penerimaan               |
| p_kode_valas           | VARCHAR(3)    | Kode mata uang                   |
| p_kurs                 | NUMERIC(15,2) | Kurs                             |
| p_kode_supplier        | VARCHAR(5)    | Kode supplier                    |
| p_jatuh_tempo          | DATE          | Jatuh tempo                      |
| p_no_faktur            | VARCHAR(50)   | Nomor faktur                     |
| p_total                | NUMERIC(15,2) | Total pembelian                  |
| p_disc                 | NUMERIC(5,2)  | Diskon                           |
| p_pajak                | NUMERIC(5,2)  | Pajak                            |
| p_grand_total          | NUMERIC(15,2) | Grand total                      |
| p_kode_barang          | VARCHAR(30)   | Kode barang                      |
| p_qty_supply           | INT           | Jumlah barang diterima           |
| p_harga                | NUMERIC(15,2) | Harga barang                     |
| p_harga_nett           | NUMERIC(15,2) | Harga nett barang                |
| p_diskon1              | NUMERIC(5,2)  | Diskon 1 per barang              |
| p_diskon2              | NUMERIC(5,2)  | Diskon 2 per barang              |

**Dependensi:**
- part_penerimaan, part_penerimaan_detail, d_barang, kartu_stok, journal

**Ringkasan Logika:**
- Insert ke part_penerimaan jika belum ada
- Insert jurnal pembelian (debit persediaan, hutang, pajak)
- Insert part_penerimaan_detail
- Update/insert stok barang di d_barang
- Insert kartu_stok sesuai penerimaan

---

### 8. **sp_pembatalan_invoice**
**Fungsi:** Membatalkan invoice, mengembalikan stok, menghapus jurnal, dan update status invoice.

**Parameter:**
| Nama Parameter   | Tipe Data      | Keterangan                       |
|------------------|---------------|-----------------------------------|
| p_kode_divisi    | VARCHAR(5)    | Kode divisi                      |
| p_no_invoice     | VARCHAR(15)   | Nomor invoice                    |

**Dependensi:**
- kartu_stok, d_barang, invoice, invoice_detail, journal

**Ringkasan Logika:**
- Loop kartu_stok terkait invoice, kembalikan stok di d_barang
- Hapus kartu_stok dan jurnal terkait
- Update status dan nilai invoice serta invoice_detail menjadi 'Cancel' dan 0
- Panggil sp_rekalkulasi untuk update qty kartu_stok

---

### 9. **sp_rekalkulasi**
**Fungsi:** Menghitung ulang quantity pada kartu_stok berdasarkan histori transaksi barang.

**Parameter:**
| Nama Parameter   | Tipe Data      | Keterangan                       |
|------------------|---------------|-----------------------------------|
| p_kode_divisi    | VARCHAR(5)    | Kode divisi                      |
| p_kode_barang    | VARCHAR(30)   | Kode barang                      |

**Dependensi:**
- kartu_stok

**Ringkasan Logika:**
- Loop seluruh kartu_stok untuk barang tersebut, hitung qty kumulatif (increase - decrease)
- Update field qty pada kartu_stok

---

### 10. **sp_retur_sales**
**Fungsi:** Mencatat retur penjualan, mengembalikan stok, dan update kartu_stok.

**Parameter:**
| Nama Parameter   | Tipe Data      | Keterangan                       |
|------------------|---------------|-----------------------------------|
| p_kode_divisi    | VARCHAR(4)    | Kode divisi                      |
| p_no_retur       | VARCHAR(15)   | Nomor retur                      |
| p_kode_cust      | VARCHAR(5)    | Kode customer                    |
| p_ket            | TEXT          | Keterangan                       |
| p_total          | NUMERIC(15,2) | Total retur                      |
| p_no_invoice     | VARCHAR(15)   | Nomor invoice asal               |
| p_kode_barang    | VARCHAR(30)   | Kode barang                      |
| p_qty_retur      | INT           | Jumlah barang diretur            |
| p_harga_nett     | NUMERIC(15,2) | Harga nett barang                |

**Dependensi:**
- return_sales, return_sales_detail, d_barang, kartu_stok

**Ringkasan Logika:**
- Insert ke return_sales jika belum ada
- Insert return_sales_detail
- Loop kartu_stok invoice, kembalikan stok ke d_barang sesuai urutan HPP
- Insert kartu_stok untuk setiap pengembalian

---

### 11. **sp_set_nomor**
**Fungsi:** Meng-generate dan mengatur nomor dokumen otomatis per bulan/tahun.

**Parameter:**
| Nama Parameter   | Tipe Data      | Keterangan                       |
|------------------|---------------|-----------------------------------|
| p_kode_divisi    | VARCHAR(4)    | Kode divisi                      |
| p_kode_dok       | VARCHAR(50)   | Kode dokumen                     |
| p_nomor (INOUT)  | VARCHAR(15)   | Nomor dokumen (hasil generate)   |

**Dependensi:**
- m_dokumen

**Ringkasan Logika:**
- Cek apakah dokumen sudah ada di m_dokumen
- Jika ada, cek tahun/bulan, increment atau reset sequence
- Update atau insert nomor baru ke m_dokumen
- Return nomor baru ke parameter output

---

### 12. **sp_tambah_saldo**
**Fungsi:** Menambah saldo rekening bank dan mencatat mutasi ke saldo_bank.

**Parameter:**
| Nama Parameter   | Tipe Data      | Keterangan                       |
|------------------|---------------|-----------------------------------|
| p_kode_divisi    | VARCHAR(5)    | Kode divisi                      |
| p_no_rekening    | VARCHAR(50)   | Nomor rekening bank              |
| p_keterangan     | VARCHAR(500)  | Keterangan                       |
| p_nominal        | NUMERIC(15,2) | Nominal penambahan saldo         |

**Dependensi:**
- d_bank, saldo_bank

**Ringkasan Logika:**
- Ambil saldo terakhir dari d_bank
- Tambahkan nominal ke saldo
- Insert mutasi ke saldo_bank
- Update saldo di d_bank

---

### 13. **sp_tanda_terima**
**Fungsi:** Membuat tanda terima (TT), menghubungkan ke invoice/retur, dan update referensi.

**Parameter:**
| Nama Parameter   | Tipe Data      | Keterangan                       |
|------------------|---------------|-----------------------------------|
| p_no_tt          | VARCHAR(15)   | Nomor tanda terima                |
| p_kode_cust      | VARCHAR(5)    | Kode customer                     |
| p_ket            | VARCHAR(500)  | Keterangan                        |
| p_no_ref         | VARCHAR(15)   | Nomor referensi (invoice/retur)   |

**Dependensi:**
- m_tt, d_tt, invoice, return_sales

**Ringkasan Logika:**
- Insert ke m_tt jika belum ada
- Insert ke d_tt
- Update field tt di invoice dan return_sales sesuai referensi

---

### 14. **sp_voucher**
**Fungsi:** Membuat data voucher komisi sales dan menghubungkan ke penerimaan.

**Parameter:**
| Nama Parameter   | Tipe Data      | Keterangan                       |
|------------------|---------------|-----------------------------------|
| p_no_voucher     | VARCHAR(15)   | Nomor voucher                    |
| p_kode_sales     | VARCHAR(5)    | Kode sales                       |
| p_total_omzet    | NUMERIC(15,2) | Total omzet                      |
| p_komisi         | NUMERIC(5,2)  | Persentase komisi                |
| p_jumlah_komisi  | NUMERIC(15,2) | Jumlah komisi                    |
| p_no_ref         | VARCHAR(15)   | Nomor referensi (penerimaan)     |

**Dependensi:**
- m_voucher, d_voucher, penerimaan_finance

**Ringkasan Logika:**
- Insert ke m_voucher jika belum ada
- Insert ke d_voucher
- Update field no_voucher di penerimaan_finance

---
