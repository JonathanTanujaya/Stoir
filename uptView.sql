-- =================================================================
-- POSTGRESQL VIEWS UNTUK SISTEM MANAJEMEN INVENTORY & KEUANGAN
-- =================================================================
-- File: uptView.txt (Optimized PostgreSQL Views)
-- Author: Database Migration Expert
-- Date: 2025-09-03
-- 
-- Deskripsi: 
-- File ini berisi definisi VIEW yang telah dioptimasi untuk PostgreSQL
-- dengan penamaan snake_case dan foreign key yang konsisten.
-- 
-- Total Views: 24 views
-- - 18 views utama (migrasi dari MSSQL)
-- - 6 views tambahan untuk monitoring dan reporting
-- =================================================================

-- 1. VIEW v_bank
-- Deskripsi: Menampilkan daftar rekening bank dengan detail bank
CREATE OR REPLACE VIEW v_bank AS
SELECT 
    db.kode_divisi, 
    db.no_rekening, 
    db.kode_bank, 
    mb.nama_bank, 
    db.atas_nama, 
    db.status
FROM d_bank db
RIGHT JOIN m_bank mb ON mb.kode_divisi = db.kode_divisi AND mb.kode_bank = db.kode_bank;

-- 2. VIEW v_barang  
-- Deskripsi: Menampilkan master barang dengan detail stok dan harga
CREATE OR REPLACE VIEW v_barang AS
SELECT 
    mb.kode_divisi, 
    mb.kode_barang, 
    mb.nama_barang, 
    mb.kode_kategori, 
    mb.harga_list, 
    mb.harga_jual, 
    db.tgl_masuk,
    db.modal, 
    db.stok, 
    mb.satuan, 
    mb.merk, 
    mb.disc1, 
    mb.disc2, 
    mb.barcode, 
    mb.status, 
    db.id,
    mb.lokasi
FROM m_barang mb
LEFT JOIN d_barang db ON mb.kode_divisi = db.kode_divisi AND mb.kode_barang = db.kode_barang;

-- 3. VIEW v_customer_resi
CREATE OR REPLACE VIEW v_customer_resi AS
SELECT 
    mr.kode_divisi, 
    mr.no_resi, 
    mr.no_rekening_tujuan, 
    mr.tgl_pembayaran, 
    mr.kode_cust, 
    mc.nama_cust, 
    mr.jumlah, 
    mr.sisa_resi, 
    mr.keterangan,
    mr.status, 
    db.kode_bank, 
    mb.nama_bank
FROM m_resi mr
LEFT JOIN d_bank db ON mr.kode_divisi = db.kode_divisi AND mr.no_rekening_tujuan = db.no_rekening
LEFT JOIN m_bank mb ON db.kode_divisi = mb.kode_divisi AND db.kode_bank = mb.kode_bank
LEFT JOIN m_cust mc ON mr.kode_divisi = mc.kode_divisi AND mr.kode_cust = mc.kode_cust;

-- 4. VIEW v_cust_retur
CREATE OR REPLACE VIEW v_cust_retur AS
SELECT 
    rs.kode_divisi, 
    rs.no_retur, 
    rs.tgl_retur, 
    rs.kode_cust, 
    mc.nama_cust, 
    rs.total, 
    rs.sisa_retur, 
    rs.keterangan,
    rs.status
FROM return_sales rs
LEFT JOIN m_cust mc ON rs.kode_divisi = mc.kode_divisi AND rs.kode_cust = mc.kode_cust;

-- 5. VIEW v_invoice
CREATE OR REPLACE VIEW v_invoice AS
SELECT 
    i.no_invoice, 
    i.tgl_faktur, 
    i.kode_cust, 
    mc.nama_cust, 
    i.kode_sales, 
    ms.nama_sales, 
    mc.kode_divisi, 
    mc.kode_area, 
    ma.area,
    i.tipe, 
    i.jatuh_tempo, 
    i.grand_total, 
    i.sisa_invoice, 
    i.ket, 
    i.status, 
    id.kode_barang, 
    mb.nama_barang, 
    mb.kode_kategori,
    mk.kategori, 
    id.qty_supply, 
    id.harga_jual, 
    id.jenis, 
    id.diskon1, 
    id.diskon2, 
    id.harga_nett,
    id.status AS status_detail, 
    id.id, 
    mb.merk, 
    mc.alamat AS alamat_cust, 
    i.total, 
    i.disc, 
    i.pajak, 
    c.company_name,
    c.alamat, 
    c.kota, 
    c.an, 
    mc.telp AS telp_cust, 
    mc.no_npwp AS npwp_cust, 
    c.telp, 
    c.npwp, 
    mb.satuan, 
    i.username,
    i.tt
FROM invoice i
JOIN invoice_detail id ON i.kode_divisi = id.kode_divisi AND i.no_invoice = id.no_invoice
JOIN m_barang mb ON id.kode_divisi = mb.kode_divisi AND id.kode_barang = mb.kode_barang
LEFT JOIN m_kategori mk ON mb.kode_divisi = mk.kode_divisi AND mb.kode_kategori = mk.kode_kategori
LEFT JOIN m_cust mc ON i.kode_divisi = mc.kode_divisi AND i.kode_cust = mc.kode_cust
LEFT JOIN m_area ma ON mc.kode_divisi = ma.kode_divisi AND mc.kode_area = ma.kode_area
LEFT JOIN m_sales ms ON i.kode_divisi = ms.kode_divisi AND i.kode_sales = ms.kode_sales
CROSS JOIN company c;

-- 6. VIEW v_invoice_header
CREATE OR REPLACE VIEW v_invoice_header AS
SELECT 
    i.no_invoice, 
    i.tgl_faktur, 
    i.kode_cust, 
    mc.nama_cust, 
    mc.kode_area, 
    ma.area, 
    i.kode_sales, 
    ms.nama_sales, 
    i.tipe,
    i.jatuh_tempo, 
    i.grand_total, 
    i.sisa_invoice, 
    i.ket, 
    i.status, 
    i.kode_divisi, 
    i.total, 
    i.disc, 
    i.pajak,
    mc.no_npwp, 
    mc.nama_pajak, 
    mc.alamat_pajak, 
    i.username, 
    i.tt
FROM invoice i
LEFT JOIN m_sales ms ON i.kode_divisi = ms.kode_divisi AND i.kode_sales = ms.kode_sales
LEFT JOIN m_cust mc ON i.kode_divisi = mc.kode_divisi AND i.kode_cust = mc.kode_cust
LEFT JOIN m_area ma ON mc.kode_divisi = ma.kode_divisi AND mc.kode_area = ma.kode_area;

-- 7. VIEW v_journal
CREATE OR REPLACE VIEW v_journal AS
SELECT 
    j.id, 
    j.tanggal, 
    j.transaksi, 
    j.kode_coa, 
    mc.nama_coa, 
    j.keterangan, 
    j.debet, 
    j.credit
FROM journal j
INNER JOIN m_coa mc ON j.kode_coa = mc.kode_coa;

-- 8. VIEW v_kartu_stok
CREATE OR REPLACE VIEW v_kartu_stok AS
SELECT 
    ks.urut, 
    ks.kode_divisi, 
    ks.kode_barang, 
    mb.nama_barang, 
    mb.kode_kategori, 
    ks.no_ref, 
    ks.tgl_proses, 
    ks.tipe,
    ks.increase, 
    ks.decrease, 
    ks.harga_debet, 
    ks.harga_kredit, 
    ks.qty, 
    ks.hpp
FROM kartu_stok ks
INNER JOIN m_barang mb ON ks.kode_divisi = mb.kode_divisi AND ks.kode_barang = mb.kode_barang;

-- 9. VIEW v_part_penerimaan
CREATE OR REPLACE VIEW v_part_penerimaan AS
SELECT 
    pp.kode_divisi, 
    pp.no_penerimaan, 
    pp.tgl_penerimaan, 
    pp.kode_supplier, 
    ms.nama_supplier, 
    pp.jatuh_tempo,
    pp.no_faktur, 
    pp.total, 
    pp.discount, 
    pp.pajak, 
    pp.grand_total, 
    pp.status,
    ppd.kode_barang, 
    ppd.qty_supply, 
    ppd.harga, 
    ppd.diskon1, 
    ppd.diskon2,
    ppd.harga_nett, 
    mb.nama_barang
FROM part_penerimaan pp
JOIN part_penerimaan_detail ppd ON pp.kode_divisi = ppd.kode_divisi AND pp.no_penerimaan = ppd.no_penerimaan
LEFT JOIN m_supplier ms ON pp.kode_divisi = ms.kode_divisi AND pp.kode_supplier = ms.kode_supplier
LEFT JOIN m_barang mb ON ppd.kode_divisi = mb.kode_divisi AND ppd.kode_barang = mb.kode_barang;

-- 10. VIEW v_part_penerimaan_header
CREATE OR REPLACE VIEW v_part_penerimaan_header AS
SELECT 
    pp.kode_divisi, 
    pp.no_penerimaan, 
    pp.tgl_penerimaan, 
    pp.kode_supplier, 
    ms.nama_supplier, 
    pp.jatuh_tempo,
    pp.no_faktur, 
    pp.total, 
    pp.discount, 
    pp.pajak, 
    pp.grand_total, 
    pp.status
FROM part_penerimaan pp
INNER JOIN m_supplier ms ON pp.kode_divisi = ms.kode_divisi AND pp.kode_supplier = ms.kode_supplier;

-- 11. VIEW v_penerimaan_finance
CREATE OR REPLACE VIEW v_penerimaan_finance AS
SELECT 
    pf.kode_divisi, 
    pf.no_penerimaan, 
    pf.tgl_penerimaan, 
    pf.tipe, 
    pf.no_ref,
    pf.tgl_ref, 
    pf.tgl_pencairan, 
    pf.bank_ref, 
    pf.no_rek_tujuan, 
    pf.kode_cust,
    pf.jumlah, 
    pf.status, 
    mc.nama_cust
FROM penerimaan_finance pf
LEFT JOIN m_cust mc ON pf.kode_divisi = mc.kode_divisi AND pf.kode_cust = mc.kode_cust;

-- 12. VIEW v_penerimaan_finance_detail
CREATE OR REPLACE VIEW v_penerimaan_finance_detail AS
SELECT 
    pf.kode_divisi, 
    pf.no_penerimaan, 
    pf.tgl_penerimaan, 
    pf.tipe, 
    pf.no_ref, 
    pf.tgl_ref, 
    pf.tgl_pencairan, 
    pf.bank_ref, 
    pf.no_rek_tujuan, 
    pf.kode_cust,
    pf.jumlah, 
    pf.status, 
    pfd.no_invoice, 
    pfd.jumlah_invoice, 
    pfd.jumlah_bayar, 
    pfd.jumlah_dispensasi,
    pfd.status AS status_detail, 
    pfd.id, 
    i.sisa_invoice,
    pfd.sisa_invoice - pfd.jumlah_bayar - pfd.jumlah_dispensasi AS sisa_bayar, 
    mc.nama_cust, 
    i.kode_sales,
    ms.nama_sales, 
    pf.no_voucher
FROM penerimaan_finance pf
JOIN penerimaan_finance_detail pfd ON pf.kode_divisi = pfd.kode_divisi AND pf.no_penerimaan = pfd.no_penerimaan
LEFT JOIN m_cust mc ON pf.kode_divisi = mc.kode_divisi AND pf.kode_cust = mc.kode_cust
LEFT JOIN invoice i ON pfd.kode_divisi = i.kode_divisi AND pfd.no_invoice = i.no_invoice
LEFT JOIN m_sales ms ON i.kode_divisi = ms.kode_divisi AND i.kode_sales = ms.kode_sales;

-- 13. VIEW v_return_sales_detail
CREATE OR REPLACE VIEW v_return_sales_detail AS
SELECT 
    rs.kode_divisi, 
    rs.no_retur, 
    rs.tgl_retur, 
    rs.kode_cust, 
    mc.nama_cust, 
    mc.alamat AS alamat_cust, 
    rs.total,
    rsd.no_invoice, 
    i.tgl_faktur, 
    i.kode_sales, 
    ms.nama_sales, 
    rsd.kode_barang, 
    mb.nama_barang, 
    mb.satuan, 
    mb.merk,
    rsd.qty_retur, 
    rsd.harga_nett, 
    mc.telp, 
    rs.status, 
    rs.tt
FROM return_sales rs
JOIN return_sales_detail rsd ON rs.kode_divisi = rsd.kode_divisi AND rs.no_retur = rsd.no_retur
LEFT JOIN m_cust mc ON rs.kode_divisi = mc.kode_divisi AND rs.kode_cust = mc.kode_cust
LEFT JOIN invoice i ON rsd.kode_divisi = i.kode_divisi AND rsd.no_invoice = i.no_invoice
LEFT JOIN m_sales ms ON i.kode_divisi = ms.kode_divisi AND i.kode_sales = ms.kode_sales
LEFT JOIN m_barang mb ON rsd.kode_divisi = mb.kode_divisi AND rsd.kode_barang = mb.kode_barang;

-- 14. VIEW v_trans
CREATE OR REPLACE VIEW v_trans AS
SELECT 
    dt.kode_trans, 
    dt.kode_coa, 
    dt.saldo_normal, 
    mt.transaksi, 
    mc.nama_coa
FROM d_trans dt
INNER JOIN m_trans mt ON dt.kode_trans = mt.kode_trans
INNER JOIN m_coa mc ON dt.kode_coa = mc.kode_coa;

-- 15. VIEW v_tt
CREATE OR REPLACE VIEW v_tt AS
SELECT 
    mt.kode_divisi,
    mt.no_tt, 
    mt.tanggal, 
    mt.kode_cust, 
    mc.nama_cust, 
    mt.keterangan
FROM m_tt mt
LEFT JOIN m_cust mc ON mt.kode_divisi = mc.kode_divisi AND mt.kode_cust = mc.kode_cust;

-- 16. VIEW v_tt_invoice
CREATE OR REPLACE VIEW v_tt_invoice AS
SELECT 
    mt.kode_divisi,
    mt.no_tt, 
    mt.tanggal, 
    mt.kode_cust, 
    mc.nama_cust, 
    mt.keterangan, 
    dt.no_ref, 
    i.tgl_faktur, 
    ms.nama_sales, 
    i.grand_total,
    i.sisa_invoice
FROM m_tt mt
JOIN d_tt dt ON mt.kode_divisi = dt.kode_divisi AND mt.no_tt = dt.no_tt
JOIN m_cust mc ON mt.kode_divisi = mc.kode_divisi AND mt.kode_cust = mc.kode_cust
JOIN invoice i ON mt.kode_divisi = i.kode_divisi AND dt.no_ref = i.no_invoice
JOIN m_sales ms ON i.kode_divisi = ms.kode_divisi AND i.kode_sales = ms.kode_sales;

-- 17. VIEW v_tt_retur
CREATE OR REPLACE VIEW v_tt_retur AS
SELECT 
    mt.kode_divisi,
    mt.no_tt, 
    mt.tanggal, 
    mt.kode_cust, 
    mc.nama_cust, 
    dt.no_ref, 
    rs.tgl_retur, 
    rsd.kode_barang, 
    mb.nama_barang,
    rsd.qty_retur, 
    rsd.harga_nett, 
    mb.merk, 
    rs.status
FROM m_tt mt
JOIN m_cust mc ON mt.kode_divisi = mc.kode_divisi AND mt.kode_cust = mc.kode_cust
JOIN d_tt dt ON mt.kode_divisi = dt.kode_divisi AND mt.no_tt = dt.no_tt
JOIN return_sales rs ON mt.kode_divisi = rs.kode_divisi AND dt.no_ref = rs.no_retur
JOIN return_sales_detail rsd ON rs.kode_divisi = rsd.kode_divisi AND rs.no_retur = rsd.no_retur
JOIN m_barang mb ON rsd.kode_divisi = mb.kode_divisi AND rsd.kode_barang = mb.kode_barang;

-- 18. VIEW v_voucher
CREATE OR REPLACE VIEW v_voucher AS
SELECT 
    mv.kode_divisi,
    mv.no_voucher, 
    mv.tanggal, 
    mv.kode_sales, 
    ms.nama_sales, 
    mv.total_omzet, 
    mv.komisi, 
    mv.jumlah_komisi
FROM m_voucher mv
INNER JOIN m_sales ms ON mv.kode_divisi = ms.kode_divisi AND mv.kode_sales = ms.kode_sales;

-- 19. VIEW tambahan untuk monitoring (opsional)
CREATE OR REPLACE VIEW v_stok_summary AS
SELECT 
    mb.kode_divisi,
    mb.kode_barang,
    mb.nama_barang,
    mb.satuan,
    COALESCE(SUM(db.stok), 0) AS total_stok,
    mb.stok_min,
    mb.lokasi,
    CASE 
        WHEN COALESCE(SUM(db.stok), 0) <= mb.stok_min THEN 'CRITICAL'
        WHEN COALESCE(SUM(db.stok), 0) <= mb.stok_min * 1.5 THEN 'LOW'
        ELSE 'NORMAL'
    END AS status_stok
FROM m_barang mb
LEFT JOIN d_barang db ON mb.kode_divisi = db.kode_divisi AND mb.kode_barang = db.kode_barang
GROUP BY mb.kode_divisi, mb.kode_barang, mb.nama_barang, mb.satuan, mb.stok_min, mb.lokasi;

-- 20. VIEW untuk laporan keuangan
CREATE OR REPLACE VIEW v_financial_report AS
SELECT 
    j.tanggal,
    j.kode_coa,
    mc.nama_coa,
    mc.saldo_normal,
    j.transaksi,
    j.keterangan,
    j.debet,
    j.credit,
    j.debet - j.credit AS balance,
    CASE 
        WHEN mc.saldo_normal = 'Debit' THEN j.debet - j.credit
        ELSE j.credit - j.debet
    END AS adjusted_balance
FROM journal j
JOIN m_coa mc ON j.kode_coa = mc.kode_coa
ORDER BY j.tanggal, j.id;

-- 21. VIEW untuk aging report piutang
CREATE OR REPLACE VIEW v_aging_report AS
SELECT 
    i.kode_divisi,
    i.no_invoice,
    i.tgl_faktur,
    i.jatuh_tempo,
    i.kode_cust,
    mc.nama_cust,
    i.grand_total,
    i.sisa_invoice,
    i.status,
    CASE 
        WHEN CURRENT_DATE <= i.jatuh_tempo THEN 'CURRENT'
        WHEN CURRENT_DATE <= i.jatuh_tempo + 30 THEN '1-30 DAYS'
        WHEN CURRENT_DATE <= i.jatuh_tempo + 60 THEN '31-60 DAYS'
        WHEN CURRENT_DATE <= i.jatuh_tempo + 90 THEN '61-90 DAYS'
        ELSE 'OVER 90 DAYS'
    END AS aging_category,
    CURRENT_DATE - i.jatuh_tempo AS days_overdue
FROM invoice i
JOIN m_cust mc ON i.kode_divisi = mc.kode_divisi AND i.kode_cust = mc.kode_cust
WHERE i.sisa_invoice > 0 AND i.status != 'Cancel';

-- 22. VIEW untuk summary penjualan per sales
CREATE OR REPLACE VIEW v_sales_summary AS
SELECT 
    i.kode_divisi,
    i.kode_sales,
    ms.nama_sales,
    ma.area AS nama_area,
    COUNT(i.no_invoice) AS total_transaksi,
    SUM(i.grand_total) AS total_penjualan,
    AVG(i.grand_total) AS rata_rata_per_transaksi,
    SUM(i.sisa_invoice) AS total_piutang,
    EXTRACT(MONTH FROM i.tgl_faktur) AS bulan,
    EXTRACT(YEAR FROM i.tgl_faktur) AS tahun
FROM invoice i
JOIN m_sales ms ON i.kode_divisi = ms.kode_divisi AND i.kode_sales = ms.kode_sales
LEFT JOIN m_area ma ON ms.kode_divisi = ma.kode_divisi AND ms.kode_area = ma.kode_area
WHERE i.status != 'Cancel'
GROUP BY i.kode_divisi, i.kode_sales, ms.nama_sales, ma.area, 
         EXTRACT(MONTH FROM i.tgl_faktur), EXTRACT(YEAR FROM i.tgl_faktur);

-- 23. VIEW untuk monitoring return/retur
CREATE OR REPLACE VIEW v_return_summary AS
SELECT 
    rs.kode_divisi,
    rs.no_retur,
    rs.tgl_retur,
    rs.kode_cust,
    mc.nama_cust,
    rs.status,
    COUNT(rsd.id) AS total_item,
    SUM(rsd.qty_retur * rsd.harga_nett) AS total_nilai_retur,
    CASE 
        WHEN rs.tipe_retur = 'sales' THEN 'Return Sales'
        WHEN rs.tipe_retur = 'supplier' THEN 'Return to Supplier'
        ELSE 'Other'
    END AS kategori_retur
FROM return_sales rs
JOIN m_cust mc ON rs.kode_divisi = mc.kode_divisi AND rs.kode_cust = mc.kode_cust
LEFT JOIN return_sales_detail rsd ON rs.kode_divisi = rsd.kode_divisi AND rs.no_retur = rsd.no_retur
GROUP BY rs.kode_divisi, rs.no_retur, rs.tgl_retur, rs.kode_cust, mc.nama_cust, 
         rs.status, rs.tipe_retur;

-- 24. VIEW untuk dashboard overview (KPI)
CREATE OR REPLACE VIEW v_dashboard_kpi AS
SELECT 
    (SELECT COUNT(*) FROM invoice WHERE status != 'Cancel') AS total_invoice,
    (SELECT SUM(grand_total) FROM invoice WHERE status != 'Cancel') AS total_penjualan,
    (SELECT SUM(sisa_invoice) FROM invoice WHERE sisa_invoice > 0) AS total_piutang,
    (SELECT COUNT(*) FROM m_cust WHERE status = TRUE) AS total_customer_aktif,
    (SELECT COUNT(*) FROM m_barang WHERE status = TRUE) AS total_produk_aktif,
    (SELECT COUNT(*) FROM v_stok_summary WHERE status_stok = 'CRITICAL') AS produk_stok_kritis,
    (SELECT AVG(grand_total) FROM invoice WHERE status != 'Cancel' AND tgl_faktur >= CURRENT_DATE - 30) AS rata_rata_penjualan_bulanan;

-- INDEXES untuk optimasi performance VIEW
-- =======================================

-- Comment: Indexes ini akan dibuat terpisah setelah semua data di-load
/*
CREATE INDEX CONCURRENTLY idx_invoice_performance ON invoice(kode_divisi, tgl_faktur, status);
CREATE INDEX CONCURRENTLY idx_invoice_detail_performance ON invoice_detail(kode_divisi, no_invoice, kode_barang);
CREATE INDEX CONCURRENTLY idx_kartu_stok_performance ON kartu_stok(kode_divisi, kode_barang, tanggal);
CREATE INDEX CONCURRENTLY idx_journal_performance ON journal(tanggal, kode_coa);
CREATE INDEX CONCURRENTLY idx_return_sales_performance ON return_sales(kode_divisi, tgl_retur, status);
*/