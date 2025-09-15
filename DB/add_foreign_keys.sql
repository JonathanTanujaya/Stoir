    -- add_foreign_keys.sql
    -- File ini berisi semua ALTER TABLE untuk menambahkan FOREIGN KEY
    -- Jalankan setelah create_tables_pk.sql sudah dijalankan dan data sudah di-load

    -- Foreign Key untuk m_bank
    ALTER TABLE m_bank
    ADD CONSTRAINT fk_mbank_divisi 
    FOREIGN KEY (kode_divisi) REFERENCES m_divisi(kode_divisi);

    -- Foreign Key untuk d_bank
    ALTER TABLE d_bank
    ADD CONSTRAINT fk_dbank_divisi 
    FOREIGN KEY (kode_divisi) REFERENCES m_divisi(kode_divisi),
    ADD CONSTRAINT fk_dbank_mbank 
    FOREIGN KEY (kode_divisi, kode_bank) REFERENCES m_bank(kode_divisi, kode_bank);

    -- Foreign Key untuk m_area
    ALTER TABLE m_area
    ADD CONSTRAINT fk_marea_divisi 
    FOREIGN KEY (kode_divisi) REFERENCES m_divisi(kode_divisi);

    -- Foreign Key untuk m_sales
    ALTER TABLE m_sales
    ADD CONSTRAINT fk_msales_divisi 
    FOREIGN KEY (kode_divisi) REFERENCES m_divisi(kode_divisi),
    ADD CONSTRAINT fk_msales_area 
    FOREIGN KEY (kode_divisi, kode_area) REFERENCES m_area(kode_divisi, kode_area);

    -- Foreign Key untuk m_cust
    ALTER TABLE m_cust
    ADD CONSTRAINT fk_mcust_divisi 
    FOREIGN KEY (kode_divisi) REFERENCES m_divisi(kode_divisi),
    ADD CONSTRAINT fk_mcust_area 
    FOREIGN KEY (kode_divisi, kode_area) REFERENCES m_area(kode_divisi, kode_area);

    -- Foreign Key untuk m_kategori
    ALTER TABLE m_kategori
    ADD CONSTRAINT fk_mkategori_divisi 
    FOREIGN KEY (kode_divisi) REFERENCES m_divisi(kode_divisi);

    -- Foreign Key untuk m_barang
    ALTER TABLE m_barang
    ADD CONSTRAINT fk_mbarang_divisi 
    FOREIGN KEY (kode_divisi) REFERENCES m_divisi(kode_divisi),
    ADD CONSTRAINT fk_mbarang_kategori 
    FOREIGN KEY (kode_divisi, kode_kategori) REFERENCES m_kategori(kode_divisi, kode_kategori);

    -- Foreign Key untuk d_barang
    ALTER TABLE d_barang
    ADD CONSTRAINT fk_dbarang_divisi 
    FOREIGN KEY (kode_divisi) REFERENCES m_divisi(kode_divisi),
    ADD CONSTRAINT fk_dbarang_mbarang 
    FOREIGN KEY (kode_divisi, kode_barang) REFERENCES m_barang(kode_divisi, kode_barang);

    -- Foreign Key untuk m_supplier
    ALTER TABLE m_supplier
    ADD CONSTRAINT fk_msupplier_divisi 
    FOREIGN KEY (kode_divisi) REFERENCES m_divisi(kode_divisi);

    -- Foreign Key untuk d_trans
    ALTER TABLE d_trans
    ADD CONSTRAINT fk_dtrans_mtrans 
    FOREIGN KEY (kode_trans) REFERENCES m_trans(kode_trans),
    ADD CONSTRAINT fk_dtrans_coa 
    FOREIGN KEY (kode_coa) REFERENCES m_coa(kode_coa);

    -- Foreign Key untuk invoice
    ALTER TABLE invoice
    ADD CONSTRAINT fk_invoice_divisi 
    FOREIGN KEY (kode_divisi) REFERENCES m_divisi(kode_divisi),
    ADD CONSTRAINT fk_invoice_cust 
    FOREIGN KEY (kode_divisi, kode_cust) REFERENCES m_cust(kode_divisi, kode_cust),
    ADD CONSTRAINT fk_invoice_sales 
    FOREIGN KEY (kode_divisi, kode_sales) REFERENCES m_sales(kode_divisi, kode_sales);

    -- Foreign Key untuk invoice_detail
    ALTER TABLE invoice_detail
    ADD CONSTRAINT fk_idetail_invoice 
    FOREIGN KEY (kode_divisi, no_invoice) REFERENCES invoice(kode_divisi, no_invoice),
    ADD CONSTRAINT fk_idetail_barang 
    FOREIGN KEY (kode_divisi, kode_barang) REFERENCES m_barang(kode_divisi, kode_barang);

    -- Foreign Key untuk journal
    ALTER TABLE journal
    ADD CONSTRAINT fk_journal_coa 
    FOREIGN KEY (kode_coa) REFERENCES m_coa(kode_coa);

    -- Foreign Key untuk kartu_stok
    ALTER TABLE kartu_stok
    ADD CONSTRAINT fk_kartustok_divisi 
    FOREIGN KEY (kode_divisi) REFERENCES m_divisi(kode_divisi),
    ADD CONSTRAINT fk_kartustok_barang 
    FOREIGN KEY (kode_divisi, kode_barang) REFERENCES m_barang(kode_divisi, kode_barang);

    -- Foreign Key untuk part_penerimaan
    ALTER TABLE part_penerimaan
    ADD CONSTRAINT fk_partpenerimaan_divisi 
    FOREIGN KEY (kode_divisi) REFERENCES m_divisi(kode_divisi),
    ADD CONSTRAINT fk_partpenerimaan_supplier 
    FOREIGN KEY (kode_divisi, kode_supplier) REFERENCES m_supplier(kode_divisi, kode_supplier);

    -- Foreign Key untuk part_penerimaan_detail
    ALTER TABLE part_penerimaan_detail
    ADD CONSTRAINT fk_partpenerimaandetail_parent 
    FOREIGN KEY (kode_divisi, no_penerimaan) REFERENCES part_penerimaan(kode_divisi, no_penerimaan),
    ADD CONSTRAINT fk_partpenerimaandetail_barang 
    FOREIGN KEY (kode_divisi, kode_barang) REFERENCES m_barang(kode_divisi, kode_barang);

    -- Foreign Key untuk m_tt
    ALTER TABLE m_tt
    ADD CONSTRAINT fk_mtt_divisi 
    FOREIGN KEY (kode_divisi) REFERENCES m_divisi(kode_divisi),
    ADD CONSTRAINT fk_mtt_cust 
    FOREIGN KEY (kode_divisi, kode_cust) REFERENCES m_cust(kode_divisi, kode_cust);

    -- Foreign Key untuk d_tt
    ALTER TABLE d_tt
    ADD CONSTRAINT fk_dtt_mtt 
    FOREIGN KEY (kode_divisi, no_tt) REFERENCES m_tt(kode_divisi, no_tt);

    -- Foreign Key untuk m_voucher
    ALTER TABLE m_voucher
    ADD CONSTRAINT fk_mvoucher_divisi 
    FOREIGN KEY (kode_divisi) REFERENCES m_divisi(kode_divisi),
    ADD CONSTRAINT fk_mvoucher_sales 
    FOREIGN KEY (kode_divisi, kode_sales) REFERENCES m_sales(kode_divisi, kode_sales);

    -- Foreign Key untuk d_voucher
    ALTER TABLE d_voucher
    ADD CONSTRAINT fk_dvoucher_mvoucher 
    FOREIGN KEY (kode_divisi, no_voucher) REFERENCES m_voucher(kode_divisi, no_voucher);

    -- Foreign Key untuk master_user
    ALTER TABLE master_user
    ADD CONSTRAINT fk_masteruser_divisi 
    FOREIGN KEY (kode_divisi) REFERENCES m_divisi(kode_divisi);

    -- Foreign Key untuk saldo_bank
    ALTER TABLE saldo_bank
    ADD CONSTRAINT fk_saldobank_divisi 
    FOREIGN KEY (kode_divisi) REFERENCES m_divisi(kode_divisi),
    ADD CONSTRAINT fk_saldobank_dbank 
    FOREIGN KEY (kode_divisi, no_rekening) REFERENCES d_bank(kode_divisi, no_rekening);

    -- Foreign Key untuk return_sales
    ALTER TABLE return_sales
    ADD CONSTRAINT fk_returnsales_divisi 
    FOREIGN KEY (kode_divisi) REFERENCES m_divisi(kode_divisi),
    ADD CONSTRAINT fk_returnsales_cust 
    FOREIGN KEY (kode_divisi, kode_cust) REFERENCES m_cust(kode_divisi, kode_cust);

    -- Foreign Key untuk return_sales_detail
    ALTER TABLE return_sales_detail
    ADD CONSTRAINT fk_returnsalesdetail_parent 
    FOREIGN KEY (kode_divisi, no_retur) REFERENCES return_sales(kode_divisi, no_retur),
    ADD CONSTRAINT fk_returnsalesdetail_invoice 
    FOREIGN KEY (kode_divisi, no_invoice) REFERENCES invoice(kode_divisi, no_invoice),
    ADD CONSTRAINT fk_returnsalesdetail_barang 
    FOREIGN KEY (kode_divisi, kode_barang) REFERENCES m_barang(kode_divisi, kode_barang);

    -- Foreign Key untuk retur_penerimaan
    ALTER TABLE retur_penerimaan
    ADD CONSTRAINT fk_returpenerimaan_divisi 
    FOREIGN KEY (kode_divisi) REFERENCES m_divisi(kode_divisi),
    ADD CONSTRAINT fk_returpenerimaan_supplier 
    FOREIGN KEY (kode_divisi, kode_supplier) REFERENCES m_supplier(kode_divisi, kode_supplier);

    -- Foreign Key untuk retur_penerimaan_detail
    ALTER TABLE retur_penerimaan_detail
    ADD CONSTRAINT fk_returpenerimaandetail_parent 
    FOREIGN KEY (kode_divisi, no_retur) REFERENCES retur_penerimaan(kode_divisi, no_retur),
    ADD CONSTRAINT fk_returpenerimaandetail_penerimaan 
    FOREIGN KEY (kode_divisi, no_penerimaan) REFERENCES part_penerimaan(kode_divisi, no_penerimaan),
    ADD CONSTRAINT fk_returpenerimaandetail_barang 
    FOREIGN KEY (kode_divisi, kode_barang) REFERENCES m_barang(kode_divisi, kode_barang);

    -- Foreign Key untuk m_resi
    ALTER TABLE m_resi
    ADD CONSTRAINT fk_mresi_divisi 
    FOREIGN KEY (kode_divisi) REFERENCES m_divisi(kode_divisi),
    ADD CONSTRAINT fk_mresi_bank 
    FOREIGN KEY (kode_divisi, no_rekening_tujuan) REFERENCES d_bank(kode_divisi, no_rekening),
    ADD CONSTRAINT fk_mresi_cust 
    FOREIGN KEY (kode_divisi, kode_cust) REFERENCES m_cust(kode_divisi, kode_cust);

    -- Foreign Key untuk penerimaan_finance
    ALTER TABLE penerimaan_finance
    ADD CONSTRAINT fk_penerimaanfinance_divisi 
    FOREIGN KEY (kode_divisi) REFERENCES m_divisi(kode_divisi),
    ADD CONSTRAINT fk_penerimaanfinance_bank 
    FOREIGN KEY (kode_divisi, no_rek_tujuan) REFERENCES d_bank(kode_divisi, no_rekening),
    ADD CONSTRAINT fk_penerimaanfinance_cust 
    FOREIGN KEY (kode_divisi, kode_cust) REFERENCES m_cust(kode_divisi, kode_cust);

    -- Foreign Key untuk penerimaan_finance_detail
    ALTER TABLE penerimaan_finance_detail
    ADD CONSTRAINT fk_penerimaanfinancedetail_parent 
    FOREIGN KEY (kode_divisi, no_penerimaan) REFERENCES penerimaan_finance(kode_divisi, no_penerimaan),
    ADD CONSTRAINT fk_penerimaanfinancedetail_invoice 
    FOREIGN KEY (kode_divisi, no_invoice) REFERENCES invoice(kode_divisi, no_invoice);

    -- Foreign Key untuk m_dokumen
    ALTER TABLE m_dokumen
    ADD CONSTRAINT fk_mdokumen_divisi 
    FOREIGN KEY (kode_divisi) REFERENCES m_divisi(kode_divisi);

    -- Foreign Key untuk d_paket
    ALTER TABLE d_paket
    ADD CONSTRAINT fk_dpaket_divisi 
    FOREIGN KEY (kode_divisi) REFERENCES m_divisi(kode_divisi),
    ADD CONSTRAINT fk_dpaket_kategori 
    FOREIGN KEY (kode_divisi, kode_kategori) REFERENCES m_kategori(kode_divisi, kode_kategori);
