-- ================================================================
-- INSERT DATA TAHAP 1 - TABEL DASAR & REFERENSI
-- ================================================================
-- File: insert_data_tahap1.sql
-- Author: Database Migration Expert
-- Date: 2025-09-03
-- 
-- Deskripsi: 
-- Script INSERT untuk tabel-tabel dasar yang hanya butuh sedikit data
-- Jalankan setelah create_tables_pk.sql dan add_foreign_keys.sql
-- ================================================================

-- 1. TABEL COMPANY (2 data)
-- ========================
INSERT INTO company (company_name, alamat, kota, an, telp, npwp) VALUES
('PT. MAJU JAYA ABADI', 'Jl. Sudirman No. 123', 'Jakarta Pusat', 'PT. Maju Jaya Abadi', '021-12345678', '01.234.567.8-901.000'),
('CV. BERKAH SEJAHTERA', 'Jl. Thamrin No. 45', 'Jakarta Selatan', 'CV. Berkah Sejahtera', '021-87654321', '02.345.678.9-012.000');

-- 2. TABEL M_COA (Chart of Account - 15 data)
-- ==========================================
INSERT INTO m_coa (kode_coa, nama_coa, saldo_normal) VALUES
('1110', 'Kas', 'Debit'),
('1120', 'Bank BCA', 'Debit'),
('1121', 'Bank Mandiri', 'Debit'),
('1122', 'Bank BRI', 'Debit'),
('1210', 'Piutang Dagang', 'Debit'),
('1310', 'Persediaan Barang', 'Debit'),
('2110', 'Hutang Dagang', 'Kredit'),
('2120', 'Hutang Bank', 'Kredit'),
('3110', 'Modal Saham', 'Kredit'),
('4110', 'Penjualan', 'Kredit'),
('5110', 'Harga Pokok Penjualan', 'Debit'),
('6110', 'Biaya Operasional', 'Debit'),
('6120', 'Biaya Administrasi', 'Debit'),
('7110', 'Pendapatan Lain-lain', 'Kredit'),
('8110', 'Biaya Lain-lain', 'Debit');

-- 3. TABEL M_DIVISI (2 data)
-- =========================
INSERT INTO m_divisi (kode_divisi, nama_divisi) VALUES
('00001', 'Head Office'),
('00002', 'Cabang Jakarta');

-- 4. TABEL M_BANK (2 data)
-- =======================
INSERT INTO m_bank (kode_divisi, kode_bank, nama_bank, status) VALUES
('00001', 'BCA01', 'Bank Central Asia', TRUE),
('00001', 'MDR01', 'Bank Mandiri', TRUE);

-- 5. TABEL D_BANK (4 data)
-- =======================
INSERT INTO d_bank (kode_divisi, no_rekening, kode_bank, atas_nama, status) VALUES
('00001', '1234567890', 'BCA01', 'PT. MAJU JAYA ABADI', TRUE),
('00001', '0987654321', 'BCA01', 'PT. MAJU JAYA ABADI - OPERASIONAL', TRUE),
('00001', '1122334455', 'MDR01', 'PT. MAJU JAYA ABADI', TRUE),
('00001', '5544332211', 'MDR01', 'PT. MAJU JAYA ABADI - PAYROLL', TRUE);

-- 6. TABEL SALDO_BANK (4 data)
-- ===========================
INSERT INTO saldo_bank (kode_divisi, no_rekening, tgl_proses, keterangan, debet, kredit, saldo) VALUES
('00001', '1234567890', '2025-01-01', 'Saldo Awal Tahun', 0.00, 0.00, 50000000.00),
('00001', '0987654321', '2025-01-01', 'Saldo Awal Tahun', 0.00, 0.00, 25000000.00),
('00001', '1122334455', '2025-01-01', 'Saldo Awal Tahun', 0.00, 0.00, 75000000.00),
('00001', '5544332211', '2025-01-01', 'Saldo Awal Tahun', 0.00, 0.00, 15000000.00);

-- 7. TABEL M_TRANS (10 data)
-- =========================
INSERT INTO m_trans (kode_trans, transaksi) VALUES
('TRX001', 'Penjualan Tunai'),
('TRX002', 'Penjualan Kredit'),
('TRX003', 'Penerimaan Kas'),
('TRX004', 'Pengeluaran Kas'),
('TRX005', 'Return Penjualan'),
('TRX006', 'Pembelian Tunai'),
('TRX007', 'Pembelian Kredit'),
('TRX008', 'Pembayaran Hutang'),
('TRX009', 'Jurnal Penyesuaian'),
('TRX010', 'Transfer Bank');

-- 8. TABEL D_TRANS (20 data - mapping transaksi ke COA)
-- ====================================================
INSERT INTO d_trans (kode_trans, kode_coa, saldo_normal) VALUES
-- Penjualan Tunai
('TRX001', '1110', 'Debit'),   -- Kas
('TRX001', '4110', 'Kredit'),  -- Penjualan
-- Penjualan Kredit  
('TRX002', '1210', 'Debit'),   -- Piutang Dagang
('TRX002', '4110', 'Kredit'),  -- Penjualan
-- Penerimaan Kas
('TRX003', '1110', 'Debit'),   -- Kas
('TRX003', '1210', 'Kredit'),  -- Piutang Dagang
-- Pengeluaran Kas
('TRX004', '6110', 'Debit'),   -- Biaya Operasional
('TRX004', '1110', 'Kredit'),  -- Kas
-- Return Penjualan
('TRX005', '4110', 'Debit'),   -- Penjualan (retur)
('TRX005', '1110', 'Kredit'),  -- Kas
-- Pembelian Tunai
('TRX006', '1310', 'Debit'),   -- Persediaan
('TRX006', '1110', 'Kredit'),  -- Kas
-- Pembelian Kredit
('TRX007', '1310', 'Debit'),   -- Persediaan
('TRX007', '2110', 'Kredit'),  -- Hutang Dagang
-- Pembayaran Hutang
('TRX008', '2110', 'Debit'),   -- Hutang Dagang
('TRX008', '1110', 'Kredit'),  -- Kas
-- Jurnal Penyesuaian
('TRX009', '6120', 'Debit'),   -- Biaya Admin
('TRX009', '1110', 'Kredit'),  -- Kas
-- Transfer Bank
('TRX010', '1120', 'Debit'),   -- Bank BCA
('TRX010', '1121', 'Kredit');  -- Bank Mandiri

-- ================================================================
-- VERIFICATION QUERIES
-- ================================================================
-- Uncomment untuk verifikasi data

/*
SELECT 'COMPANY' as tabel, COUNT(*) as total FROM company
UNION ALL
SELECT 'M_COA' as tabel, COUNT(*) as total FROM m_coa
UNION ALL  
SELECT 'M_DIVISI' as tabel, COUNT(*) as total FROM m_divisi
UNION ALL
SELECT 'M_BANK' as tabel, COUNT(*) as total FROM m_bank
UNION ALL
SELECT 'D_BANK' as tabel, COUNT(*) as total FROM d_bank
UNION ALL
SELECT 'SALDO_BANK' as tabel, COUNT(*) as total FROM saldo_bank
UNION ALL
SELECT 'M_TRANS' as tabel, COUNT(*) as total FROM m_trans
UNION ALL
SELECT 'D_TRANS' as tabel, COUNT(*) as total FROM d_trans;
*/

-- ================================================================
-- CATATAN TAHAP SELANJUTNYA
-- ================================================================
/*
TAHAP 2: M_AREA, M_SALES, M_CUST, M_KATEGORI, M_BARANG, D_BARANG
TAHAP 3: M_SUPPLIER, PART_PENERIMAAN, PART_PENERIMAAN_DETAIL  
TAHAP 4: INVOICE, INVOICE_DETAIL, JOURNAL, KARTU_STOK
TAHAP 5: RETURN_SALES, RETURN_SALES_DETAIL, M_TT, D_TT
TAHAP 6: M_VOUCHER, D_VOUCHER, MASTER_USER, M_RESI
TAHAP 7: PENERIMAAN_FINANCE, PENERIMAAN_FINANCE_DETAIL
TAHAP 8: RETUR_PENERIMAAN, RETUR_PENERIMAAN_DETAIL, M_DOKUMEN, D_PAKET
*/
