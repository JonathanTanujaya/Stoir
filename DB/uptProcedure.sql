-- 1. PROCEDURE sp_invoice
CREATE OR REPLACE PROCEDURE sp_invoice(
    p_kode_divisi VARCHAR(5),
    p_no_invoice VARCHAR(15),
    p_kode_cust VARCHAR(5),
    p_kode_sales VARCHAR(5),
    p_tipe CHAR(1),
    p_total NUMERIC(15,2),
    p_disc NUMERIC(5,2),
    p_pajak NUMERIC(5,2),
    p_grand_total NUMERIC(15,2),
    p_ket TEXT,
    p_kode_barang VARCHAR(50),
    p_qty_supply INT,
    p_harga_jual NUMERIC(15,2),
    p_diskon1 NUMERIC(5,2),
    p_diskon2 NUMERIC(5,2),
    p_harga_nett NUMERIC(15,2),
    p_user VARCHAR(50)
)
LANGUAGE plpgsql
AS $$
DECLARE
    v_jatuhtempo DATE;
    v_top INT;
    v_idbarang BIGINT;
    v_stoks BIGINT;
    v_qtybaru BIGINT;
    v_modal NUMERIC(15,2);
    v_qty_supply_remaining INT := p_qty_supply;
    cur_stok CURSOR FOR 
        SELECT id, stok, modal 
        FROM d_barang 
        WHERE kode_divisi = p_kode_divisi 
          AND kode_barang = p_kode_barang 
          AND stok > 0 
        ORDER BY tgl_masuk ASC;
BEGIN
    -- Cek jika invoice sudah ada
    IF NOT EXISTS (SELECT 1 FROM invoice WHERE kode_divisi = p_kode_divisi AND no_invoice = p_no_invoice) THEN
        -- Get jatuh tempo from customer
        SELECT jatuh_tempo INTO v_top 
        FROM m_cust 
        WHERE kode_divisi = p_kode_divisi AND kode_cust = p_kode_cust;
        
        v_jatuhtempo := CURRENT_DATE + v_top;
        
        -- Insert into invoice
        INSERT INTO invoice(
            kode_divisi, no_invoice, tgl_faktur, kode_cust, kode_sales, 
            tipe, jatuh_tempo, total, disc, pajak, grand_total, 
            sisa_invoice, ket, status, username
        ) VALUES (
            p_kode_divisi, p_no_invoice, CURRENT_DATE, p_kode_cust, p_kode_sales,
            p_tipe, v_jatuhtempo, p_total, p_disc, p_pajak, p_grand_total,
            p_grand_total, p_ket, 'Open', p_user
        );
    END IF;
    
    -- Insert into invoice_detail
    INSERT INTO invoice_detail(
        kode_divisi, no_invoice, kode_barang, qty_supply, 
        harga_jual, diskon1, diskon2, harga_nett, status
    ) VALUES (
        p_kode_divisi, p_no_invoice, p_kode_barang, p_qty_supply,
        p_harga_jual, p_diskon1, p_diskon2, p_harga_nett, 'Open'
    );
    
    -- Process stock reduction using cursor
    OPEN cur_stok;
    LOOP
        FETCH cur_stok INTO v_idbarang, v_stoks, v_modal;
        EXIT WHEN NOT FOUND OR v_qty_supply_remaining <= 0;
        
        IF v_qty_supply_remaining > 0 THEN
            IF v_qty_supply_remaining < v_stoks THEN
                -- Partial reduction
                UPDATE d_barang SET stok = stok - v_qty_supply_remaining 
                WHERE id = v_idbarang;
                
                -- Get new total stock
                SELECT COALESCE(SUM(stok), 0) INTO v_qtybaru 
                FROM d_barang 
                WHERE kode_barang = p_kode_barang;
                
                -- Insert into kartu_stok
                INSERT INTO kartu_stok(
                    kode_barang, no_ref, tgl_proses, tipe, 
                    increase, decrease, harga_debet, harga_kredit, 
                    qty, hpp, kode_divisi
                ) VALUES (
                    p_kode_barang, p_no_invoice, CURRENT_TIMESTAMP, 'Penjualan',
                    0, v_qty_supply_remaining, 0, 
                    p_harga_nett - (p_harga_nett * p_disc / 100),
                    v_qtybaru, v_modal, p_kode_divisi
                );
                
                v_qty_supply_remaining := 0;
            ELSE
                -- Full reduction
                UPDATE d_barang SET stok = 0 WHERE id = v_idbarang;
                
                -- Get new total stock
                SELECT COALESCE(SUM(stok), 0) INTO v_qtybaru 
                FROM d_barang 
                WHERE kode_barang = p_kode_barang;
                
                -- Insert into kartu_stok
                INSERT INTO kartu_stok(
                    kode_barang, no_ref, tgl_proses, tipe, 
                    increase, decrease, harga_debet, harga_kredit, 
                    qty, hpp, kode_divisi
                ) VALUES (
                    p_kode_barang, p_no_invoice, CURRENT_TIMESTAMP, 'Penjualan',
                    0, v_stoks, 0, 
                    p_harga_nett - (p_harga_nett * p_disc / 100),
                    v_qtybaru, v_modal, p_kode_divisi
                );
                
                v_qty_supply_remaining := v_qty_supply_remaining - v_stoks;
            END IF;
        END IF;
    END LOOP;
    
    CLOSE cur_stok;
END;
$$;

-- 2. PROCEDURE sp_journal_invoice
CREATE OR REPLACE PROCEDURE sp_journal_invoice(p_no_invoice VARCHAR(15))
LANGUAGE plpgsql
AS $$
DECLARE
    v_grand_total NUMERIC(15,2);
    v_total NUMERIC(15,2);
    v_hpp NUMERIC(15,2);
    v_insentif NUMERIC(5,2) := 1;
BEGIN
    -- Get invoice values
    SELECT grand_total, total INTO v_grand_total, v_total
    FROM invoice WHERE no_invoice = p_no_invoice;
    
    -- Calculate HPP
    SELECT COALESCE(SUM(hpp * decrease), 0) INTO v_hpp
    FROM kartu_stok WHERE no_ref = p_no_invoice;
    
    -- Insert journal entries
    -- Debit Piutang
    INSERT INTO journal(tanggal, transaksi, kode_coa, keterangan, debet, credit)
    VALUES (CURRENT_DATE, 'Penjualan', '1-1210', p_no_invoice, v_grand_total, 0);
    
    -- Credit Pendapatan
    INSERT INTO journal(tanggal, transaksi, kode_coa, keterangan, debet, credit)
    VALUES (CURRENT_DATE, 'Penjualan', '4-1001', p_no_invoice, 0, v_total);
    
    -- Credit Pajak
    INSERT INTO journal(tanggal, transaksi, kode_coa, keterangan, debet, credit)
    VALUES (CURRENT_DATE, 'Penjualan', '2-1301', p_no_invoice, 0, v_grand_total - v_total);
    
    -- Debit HPP
    INSERT INTO journal(tanggal, transaksi, kode_coa, keterangan, debet, credit)
    VALUES (CURRENT_DATE, 'Penjualan', '5-1001', p_no_invoice, v_hpp, 0);
    
    -- Credit Persediaan
    INSERT INTO journal(tanggal, transaksi, kode_coa, keterangan, debet, credit)
    VALUES (CURRENT_DATE, 'Penjualan', '1-1301', p_no_invoice, 0, v_hpp);
    
    -- Debit Beban Insentif
    INSERT INTO journal(tanggal, transaksi, kode_coa, keterangan, debet, credit)
    VALUES (CURRENT_DATE, 'Penjualan', '6-1020', p_no_invoice, v_insentif * v_total / 100, 0);
    
    -- Credit Hutang Insentif
    INSERT INTO journal(tanggal, transaksi, kode_coa, keterangan, debet, credit)
    VALUES (CURRENT_DATE, 'Penjualan', '2-1470', p_no_invoice, 0, v_insentif * v_total / 100);
END;
$$;

-- 3. PROCEDURE sp_journal_retur_sales
CREATE OR REPLACE PROCEDURE sp_journal_retur_sales(p_no_retur VARCHAR(15))
LANGUAGE plpgsql
AS $$
DECLARE
    v_total_retur NUMERIC(15,2);
    v_hpp NUMERIC(15,2);
    v_insentif NUMERIC(5,2) := 1;
BEGIN
    -- Get return values
    SELECT total INTO v_total_retur FROM return_sales WHERE no_retur = p_no_retur;
    
    -- Calculate HPP
    SELECT COALESCE(SUM(hpp), 0) INTO v_hpp
    FROM kartu_stok WHERE no_ref = p_no_retur;
    
    -- Insert journal entries
    -- Debit Pendapatan (reversal)
    INSERT INTO journal(tanggal, transaksi, kode_coa, keterangan, debet, credit)
    VALUES (CURRENT_DATE, 'Retur Penjualan', '4-1001', p_no_retur, v_total_retur / 1.1, 0);
    
    -- Debit Pajak (reversal)
    INSERT INTO journal(tanggal, transaksi, kode_coa, keterangan, debet, credit)
    VALUES (CURRENT_DATE, 'Retur Penjualan', '2-1301', p_no_retur, v_total_retur - (v_total_retur / 1.1), 0);
    
    -- Credit Piutang (reversal)
    INSERT INTO journal(tanggal, transaksi, kode_coa, keterangan, debet, credit)
    VALUES (CURRENT_DATE, 'Retur Penjualan', '1-1210', p_no_retur, 0, v_total_retur);
    
    -- Debit Persediaan
    INSERT INTO journal(tanggal, transaksi, kode_coa, keterangan, debet, credit)
    VALUES (CURRENT_DATE, 'Retur Penjualan', '1-1301', p_no_retur, v_hpp, 0);
    
    -- Credit HPP
    INSERT INTO journal(tanggal, transaksi, kode_coa, keterangan, debet, credit)
    VALUES (CURRENT_DATE, 'Retur Penjualan', '5-1001', p_no_retur, 0, v_hpp);
    
    -- Debit Hutang Insentif (reversal)
    INSERT INTO journal(tanggal, transaksi, kode_coa, keterangan, debet, credit)
    VALUES (CURRENT_DATE, 'Retur Penjualan', '2-1470', p_no_retur, v_insentif * (v_total_retur / 1.1) / 100, 0);
    
    -- Credit Beban Insentif (reversal)
    INSERT INTO journal(tanggal, transaksi, kode_coa, keterangan, debet, credit)
    VALUES (CURRENT_DATE, 'Retur Penjualan', '6-1020', p_no_retur, 0, v_insentif * (v_total_retur / 1.1) / 100);
END;
$$;

-- 4. PROCEDURE sp_master_resi
CREATE OR REPLACE PROCEDURE sp_master_resi(
    p_kode_divisi VARCHAR(5),
    p_no_resi VARCHAR(15),
    p_no_rekening_tujuan VARCHAR(50),
    p_tgl_bayar DATE,
    p_kode_cust VARCHAR(50),
    p_keterangan TEXT,
    p_nominal NUMERIC(15,2)
)
LANGUAGE plpgsql
AS $$
DECLARE
    v_saldo NUMERIC(15,2);
BEGIN
    -- Insert into m_resi
    INSERT INTO m_resi (
        kode_divisi, no_resi, no_rekening_tujuan, tgl_pembayaran,
        kode_cust, jumlah, sisa_resi, keterangan, status
    ) VALUES (
        p_kode_divisi, p_no_resi, p_no_rekening_tujuan, p_tgl_bayar,
        p_kode_cust, p_nominal, p_nominal, p_keterangan, 'Open'
    );
    
    -- Update bank balance
    SELECT COALESCE(saldo, 0) INTO v_saldo 
    FROM d_bank 
    WHERE kode_divisi = p_kode_divisi AND no_rekening = p_no_rekening_tujuan;
    
    v_saldo := v_saldo + p_nominal;
    
    -- Insert into saldo_bank
    INSERT INTO saldo_bank(
        kode_divisi, no_rekening, tgl_proses, keterangan,
        debet, kredit, saldo
    ) VALUES (
        p_kode_divisi, p_no_rekening_tujuan, CURRENT_DATE, p_keterangan,
        p_nominal, 0, v_saldo
    );
    
    -- Update d_bank
    UPDATE d_bank SET saldo = v_saldo 
    WHERE kode_divisi = p_kode_divisi AND no_rekening = p_no_rekening_tujuan;
END;
$$;

-- 5. PROCEDURE sp_merge_barang
CREATE OR REPLACE PROCEDURE sp_merge_barang(
    p_kode_divisi VARCHAR(4),
    p_no_create VARCHAR(15),
    p_kode_barang_create VARCHAR(30),
    p_qty_create INT,
    p_modal_create NUMERIC(15,2),
    p_biaya NUMERIC(15,2),
    p_kode_barang_merge VARCHAR(30),
    p_qty_merge INT,
    p_modal_merge NUMERIC(15,2),
    p_id BIGINT
)
LANGUAGE plpgsql
AS $$
DECLARE
    v_qty_baru INT;
BEGIN
    -- Check if merge record exists
    IF NOT EXISTS (SELECT 1 FROM merge_barang WHERE kode_divisi = p_kode_divisi AND no_create = p_no_create) THEN
        -- Insert into merge_barang
        INSERT INTO merge_barang(
            kode_divisi, no_create, tanggal, kode_barang,
            qty, modal, biaya_tambahan
        ) VALUES (
            p_kode_divisi, p_no_create, CURRENT_DATE, p_kode_barang_create,
            p_qty_create, p_modal_create, p_biaya
        );
        
        -- Check if barang exists
        IF NOT EXISTS (
            SELECT 1 FROM d_barang 
            WHERE kode_divisi = p_kode_divisi 
              AND kode_barang = p_kode_barang_create 
              AND modal = p_modal_create
        ) THEN
            -- Insert new barang
            INSERT INTO d_barang(
                kode_divisi, kode_barang, tgl_masuk, modal, stok
            ) VALUES (
                p_kode_divisi, p_kode_barang_create, CURRENT_TIMESTAMP, p_modal_create, p_qty_create
            );
        ELSE
            -- Update existing barang
            UPDATE d_barang SET stok = stok + p_qty_create
            WHERE kode_divisi = p_kode_divisi 
              AND kode_barang = p_kode_barang_create 
              AND modal = p_modal_create;
        END IF;
        
        -- Get new quantity
        SELECT COALESCE(SUM(stok), 0) INTO v_qty_baru
        FROM d_barang 
        WHERE kode_divisi = p_kode_divisi AND kode_barang = p_kode_barang_create;
        
        -- Insert into kartu_stok
        INSERT INTO kartu_stok(
            kode_barang, no_ref, tgl_proses, tipe,
            increase, decrease, harga_debet, harga_kredit,
            qty, hpp, kode_divisi
        ) VALUES (
            p_kode_barang_create, p_no_create, CURRENT_TIMESTAMP, 'Merge Barang',
            p_qty_create, 0, p_modal_create, 0,
            v_qty_baru, p_modal_create, p_kode_divisi
        );
    END IF;
    
    -- Insert into merge_barang_detail
    INSERT INTO merge_barang_detail(
        kode_divisi, no_create, kode_barang, qty, modal
    ) VALUES (
        p_kode_divisi, p_no_create, p_kode_barang_merge, p_qty_merge, p_modal_merge
    );
    
    -- Update source barang stock
    UPDATE d_barang SET stok = stok - p_qty_merge WHERE id = p_id;
    
    -- Get new quantity for source barang
    SELECT COALESCE(SUM(stok), 0) INTO v_qty_baru
    FROM d_barang 
    WHERE kode_divisi = p_kode_divisi AND kode_barang = p_kode_barang_merge;
    
    -- Insert into kartu_stok for source barang
    INSERT INTO kartu_stok(
        kode_barang, no_ref, tgl_proses, tipe,
        increase, decrease, harga_debet, harga_kredit,
        qty, hpp, kode_divisi
    ) VALUES (
        p_kode_barang_merge, p_no_create, CURRENT_TIMESTAMP, 'Merge Barang',
        0, p_qty_merge, 0, p_modal_merge,
        v_qty_baru, p_modal_create, p_kode_divisi
    );
END;
$$;

-- 6. PROCEDURE sp_opname
CREATE OR REPLACE PROCEDURE sp_opname(
    p_kode_divisi VARCHAR(4),
    p_no_opname VARCHAR(15),
    p_total NUMERIC(15,2),
    p_kode_barang VARCHAR(30),
    p_qty INT,
    p_modal NUMERIC(15,2),
    p_id BIGINT
)
LANGUAGE plpgsql
AS $$
DECLARE
    v_qty_baru INT;
BEGIN
    -- Check if opname exists
    IF NOT EXISTS (SELECT 1 FROM opname WHERE kode_divisi = p_kode_divisi AND no_opname = p_no_opname) THEN
        -- Insert into opname
        INSERT INTO opname(
            kode_divisi, no_opname, tanggal, total
        ) VALUES (
            p_kode_divisi, p_no_opname, CURRENT_DATE, p_total
        );
    END IF;
    
    -- Insert into opname_detail
    INSERT INTO opname_detail(
        kode_divisi, no_opname, kode_barang, qty, modal
    ) VALUES (
        p_kode_divisi, p_no_opname, p_kode_barang, p_qty, p_modal
    );
    
    -- Update barang stock
    UPDATE d_barang SET stok = stok + p_qty
    WHERE kode_divisi = p_kode_divisi 
      AND kode_barang = p_kode_barang 
      AND modal = p_modal;
    
    -- Get new quantity
    SELECT COALESCE(SUM(stok), 0) INTO v_qty_baru
    FROM d_barang 
    WHERE kode_divisi = p_kode_divisi AND kode_barang = p_kode_barang;
    
    -- Insert into kartu_stok
    IF p_qty > 0 THEN
        INSERT INTO kartu_stok(
            kode_barang, no_ref, tgl_proses, tipe,
            increase, decrease, harga_debet, harga_kredit,
            qty, hpp, kode_divisi
        ) VALUES (
            p_kode_barang, p_no_opname, CURRENT_TIMESTAMP, 'Stok Opname',
            p_qty, 0, p_modal, 0,
            v_qty_baru, p_modal, p_kode_divisi
        );
    ELSE
        INSERT INTO kartu_stok(
            kode_barang, no_ref, tgl_proses, tipe,
            increase, decrease, harga_debet, harga_kredit,
            qty, hpp, kode_divisi
        ) VALUES (
            p_kode_barang, p_no_opname, CURRENT_TIMESTAMP, 'Stok Opname',
            0, ABS(p_qty), 0, p_modal,
            v_qty_baru, p_modal, p_kode_divisi
        );
    END IF;
END;
$$;

-- 7. PROCEDURE sp_part_penerimaan
CREATE OR REPLACE PROCEDURE sp_part_penerimaan(
    p_kode_divisi VARCHAR(4),
    p_no_penerimaan VARCHAR(15),
    p_tgl_penerimaan DATE,
    p_kode_valas VARCHAR(3),
    p_kurs NUMERIC(15,2),
    p_kode_supplier VARCHAR(5),
    p_jatuh_tempo DATE,
    p_no_faktur VARCHAR(50),
    p_total NUMERIC(15,2),
    p_disc NUMERIC(5,2),
    p_pajak NUMERIC(5,2),
    p_grand_total NUMERIC(15,2),
    p_kode_barang VARCHAR(30),
    p_qty_supply INT,
    p_harga NUMERIC(15,2),
    p_harga_nett NUMERIC(15,2),
    p_diskon1 NUMERIC(5,2),
    p_diskon2 NUMERIC(5,2)
)
LANGUAGE plpgsql
AS $$
DECLARE
    v_qty_lama INT;
    v_harga_nett_calc NUMERIC(15,2);
BEGIN
    -- Calculate nett price
    v_harga_nett_calc := p_harga_nett;
    
    -- Check if penerimaan exists
    IF NOT EXISTS (
        SELECT 1 FROM part_penerimaan 
        WHERE kode_divisi = p_kode_divisi AND no_penerimaan = p_no_penerimaan
    ) THEN
        -- Insert into part_penerimaan
        INSERT INTO part_penerimaan(
            kode_divisi, no_penerimaan, tgl_penerimaan, kode_valas, kurs,
            kode_supplier, jatuh_tempo, no_faktur, total, discount,
            pajak, grand_total, status
        ) VALUES (
            p_kode_divisi, p_no_penerimaan, p_tgl_penerimaan, p_kode_valas, p_kurs,
            p_kode_supplier, p_jatuh_tempo, p_no_faktur, p_total, p_disc,
            p_pajak, p_grand_total, 'Open'
        );
        
        -- Insert journal entries
        INSERT INTO journal(tanggal, transaksi, kode_coa, keterangan, debet, credit)
        VALUES (CURRENT_DATE, 'Pembelian', '1-1301', p_no_faktur, p_total, 0);
        
        INSERT INTO journal(tanggal, transaksi, kode_coa, keterangan, debet, credit)
        VALUES (CURRENT_DATE, 'Pembelian', '1-1710', p_no_faktur, p_grand_total - p_total, 0);
        
        INSERT INTO journal(tanggal, transaksi, kode_coa, keterangan, debet, credit)
        VALUES (CURRENT_DATE, 'Pembelian', '2-1110', p_no_faktur, 0, p_grand_total);
    END IF;
    
    -- Insert into part_penerimaan_detail
    INSERT INTO part_penerimaan_detail(
        kode_divisi, no_penerimaan, kode_barang, qty_supply,
        harga, diskon1, diskon2, harga_nett
    ) VALUES (
        p_kode_divisi, p_no_penerimaan, p_kode_barang, p_qty_supply,
        p_harga, p_diskon1, p_diskon2, v_harga_nett_calc
    );
    
    -- Update or insert barang
    IF EXISTS (
        SELECT 1 FROM d_barang 
        WHERE kode_divisi = p_kode_divisi 
          AND kode_barang = p_kode_barang 
          AND modal = v_harga_nett_calc
    ) THEN
        UPDATE d_barang SET stok = stok + p_qty_supply
        WHERE kode_divisi = p_kode_divisi 
          AND kode_barang = p_kode_barang 
          AND modal = v_harga_nett_calc;
    ELSE
        INSERT INTO d_barang(
            kode_divisi, kode_barang, modal, tgl_masuk, stok
        ) VALUES (
            p_kode_divisi, p_kode_barang, v_harga_nett_calc, p_tgl_penerimaan, p_qty_supply
        );
    END IF;
    
    -- Get current quantity
    SELECT COALESCE(SUM(stok), 0) INTO v_qty_lama
    FROM d_barang 
    WHERE kode_divisi = p_kode_divisi AND kode_barang = p_kode_barang;
    
    -- Insert into kartu_stok
    INSERT INTO kartu_stok(
        kode_barang, no_ref, tgl_proses, tipe,
        increase, decrease, harga_debet, harga_kredit,
        qty, kode_divisi
    ) VALUES (
        p_kode_barang, p_no_penerimaan, CURRENT_TIMESTAMP, 'Pembelian',
        p_qty_supply, 0, v_harga_nett_calc, 0,
        v_qty_lama, p_kode_divisi
    );
END;
$$;

-- 8. PROCEDURE sp_pembatalan_invoice
CREATE OR REPLACE PROCEDURE sp_pembatalan_invoice(
    p_kode_divisi VARCHAR(5),
    p_no_invoice VARCHAR(15)
)
LANGUAGE plpgsql
AS $$
DECLARE
    r_record RECORD;
    cur_data CURSOR FOR 
        SELECT kode_barang, harga_kredit, hpp, decrease, urut 
        FROM kartu_stok 
        WHERE kode_divisi = p_kode_divisi AND no_ref = p_no_invoice;
BEGIN
    -- Process each stock record
    FOR r_record IN cur_data LOOP
        -- Restore stock
        UPDATE d_barang SET stok = stok + r_record.decrease
        WHERE kode_divisi = p_kode_divisi 
          AND kode_barang = r_record.kode_barang 
          AND modal = r_record.hpp;
        
        -- Delete kartu_stok record
        DELETE FROM kartu_stok WHERE urut = r_record.urut;
        
        -- Recalculate stock
        CALL sp_rekalkulasi(p_kode_divisi, r_record.kode_barang);
    END LOOP;
    
    -- Update invoice status
    UPDATE invoice SET 
        status = 'Cancel',
        total = 0,
        disc = 0,
        pajak = 0,
        grand_total = 0,
        sisa_invoice = 0
    WHERE kode_divisi = p_kode_divisi AND no_invoice = p_no_invoice;
    
    -- Update invoice_detail status
    UPDATE invoice_detail SET status = 'Cancel'
    WHERE kode_divisi = p_kode_divisi AND no_invoice = p_no_invoice;
    
    -- Delete journal entries
    DELETE FROM journal WHERE keterangan = p_no_invoice;
END;
$$;

-- 9. PROCEDURE sp_rekalkulasi
CREATE OR REPLACE PROCEDURE sp_rekalkulasi(
    p_kode_divisi VARCHAR(5),
    p_kode_barang VARCHAR(30)
)
LANGUAGE plpgsql
AS $$
DECLARE
    v_qty INT := 0;
    r_record RECORD;
    cur_data CURSOR FOR 
        SELECT urut, tipe, increase, decrease 
        FROM kartu_stok 
        WHERE kode_divisi = p_kode_divisi AND kode_barang = p_kode_barang
        ORDER BY urut;
BEGIN
    -- Recalculate stock quantity
    FOR r_record IN cur_data LOOP
        v_qty := v_qty + r_record.increase - r_record.decrease;
        UPDATE kartu_stok SET qty = v_qty WHERE urut = r_record.urut;
    END LOOP;
END;
$$;

-- 10. PROCEDURE sp_retur_sales
CREATE OR REPLACE PROCEDURE sp_retur_sales(
    p_kode_divisi VARCHAR(4),
    p_no_retur VARCHAR(15),
    p_kode_cust VARCHAR(5),
    p_ket TEXT,
    p_total NUMERIC(15,2),
    p_no_invoice VARCHAR(15),
    p_kode_barang VARCHAR(30),
    p_qty_retur INT,
    p_harga_nett NUMERIC(15,2)
)
LANGUAGE plpgsql
AS $$
DECLARE
    v_hpp NUMERIC(15,2);
    v_decrease INT;
    v_qty_retur_remaining INT := p_qty_retur;
    v_qty_lama INT;
    cur_data CURSOR FOR 
        SELECT hpp, decrease 
        FROM kartu_stok 
        WHERE kode_divisi = p_kode_divisi 
          AND kode_barang = p_kode_barang 
          AND no_ref = p_no_invoice 
        ORDER BY hpp;
BEGIN
    -- Check if return exists
    IF NOT EXISTS (
        SELECT 1 FROM return_sales 
        WHERE kode_divisi = p_kode_divisi AND no_retur = p_no_retur
    ) THEN
        -- Insert into return_sales
        INSERT INTO return_sales(
            kode_divisi, no_retur, tgl_retur, kode_cust,
            total, sisa_retur, keterangan, status
        ) VALUES (
            p_kode_divisi, p_no_retur, CURRENT_DATE, p_kode_cust,
            p_total, p_total, p_ket, 'Open'
        );
    END IF;
    
    -- Insert into return_sales_detail
    INSERT INTO return_sales_detail(
        kode_divisi, no_retur, no_invoice, kode_barang,
        qty_retur, harga_nett, status
    ) VALUES (
        p_kode_divisi, p_no_retur, p_no_invoice, p_kode_barang,
        p_qty_retur, p_harga_nett, 'Open'
    );
    
    -- Process stock return
    FOR r_record IN cur_data LOOP
        EXIT WHEN v_qty_retur_remaining <= 0;
        
        IF v_qty_retur_remaining > 0 THEN
            IF v_qty_retur_remaining >= r_record.decrease THEN
                -- Full return
                UPDATE d_barang SET stok = stok + r_record.decrease
                WHERE kode_divisi = p_kode_divisi 
                  AND kode_barang = p_kode_barang 
                  AND modal = r_record.hpp;
                
                -- Get current quantity
                SELECT COALESCE(SUM(stok), 0) INTO v_qty_lama
                FROM d_barang 
                WHERE kode_divisi = p_kode_divisi AND kode_barang = p_kode_barang;
                
                -- Insert into kartu_stok
                INSERT INTO kartu_stok(
                    kode_barang, no_ref, tgl_proses, tipe,
                    increase, decrease, harga_debet, harga_kredit,
                    qty, hpp, kode_divisi
                ) VALUES (
                    p_kode_barang, p_no_retur, CURRENT_TIMESTAMP, 'Retur Penjualan',
                    r_record.decrease, 0, r_record.hpp, 0,
                    v_qty_lama, r_record.hpp, p_kode_divisi
                );
                
                v_qty_retur_remaining := v_qty_retur_remaining - r_record.decrease;
            ELSE
                -- Partial return
                UPDATE d_barang SET stok = stok + v_qty_retur_remaining
                WHERE kode_divisi = p_kode_divisi 
                  AND kode_barang = p_kode_barang 
                  AND modal = r_record.hpp;
                
                -- Get current quantity
                SELECT COALESCE(SUM(stok), 0) INTO v_qty_lama
                FROM d_barang 
                WHERE kode_divisi = p_kode_divisi AND kode_barang = p_kode_barang;
                
                -- Insert into kartu_stok
                INSERT INTO kartu_stok(
                    kode_barang, no_ref, tgl_proses, tipe,
                    increase, decrease, harga_debet, harga_kredit,
                    qty, hpp, kode_divisi
                ) VALUES (
                    p_kode_barang, p_no_retur, CURRENT_TIMESTAMP, 'Retur Penjualan',
                    v_qty_retur_remaining, 0, r_record.hpp, 0,
                    v_qty_lama, r_record.hpp, p_kode_divisi
                );
                
                v_qty_retur_remaining := 0;
            END IF;
        END IF;
    END LOOP;
END;
$$;

-- 11. PROCEDURE sp_set_nomor
CREATE OR REPLACE PROCEDURE sp_set_nomor(
    p_kode_divisi VARCHAR(4),
    p_kode_dok VARCHAR(50),
    INOUT p_nomor VARCHAR(15) DEFAULT NULL
)
LANGUAGE plpgsql
AS $$
DECLARE
    v_nomor_lama VARCHAR(15);
    v_nomor_baru VARCHAR(15);
    v_tahun VARCHAR(4);
    v_bulan VARCHAR(2);
BEGIN
    v_tahun := EXTRACT(YEAR FROM CURRENT_DATE)::VARCHAR;
    v_bulan := LPAD(EXTRACT(MONTH FROM CURRENT_DATE)::VARCHAR, 2, '0');
    
    -- Check if document exists
    IF EXISTS (
        SELECT 1 FROM m_dokumen 
        WHERE kode_divisi = p_kode_divisi AND kode_dok = p_kode_dok
    ) THEN
        -- Get current number
        SELECT nomor INTO v_nomor_lama
        FROM m_dokumen 
        WHERE kode_divisi = p_kode_divisi AND kode_dok = p_kode_dok;
        
        -- Check if year and month match
        IF LEFT(v_nomor_lama, 4) = v_tahun AND SUBSTRING(v_nomor_lama FROM 6 FOR 2) = v_bulan THEN
            -- Increment sequence
            v_nomor_baru := v_tahun || '/' || v_bulan || '/' || p_kode_dok || '/' || 
                           LPAD((RIGHT(v_nomor_lama, 4)::INT + 1)::VARCHAR, 4, '0');
        ELSE
            -- Start new sequence
            v_nomor_baru := v_tahun || '/' || v_bulan || '/' || p_kode_dok || '/0001';
        END IF;
        
        -- Update document number
        UPDATE m_dokumen SET nomor = v_nomor_baru
        WHERE kode_divisi = p_kode_divisi AND kode_dok = p_kode_dok;
    ELSE
        -- Create new document entry
        v_nomor_baru := v_tahun || '/' || v_bulan || '/' || p_kode_dok || '/0001';
        INSERT INTO m_dokumen(kode_divisi, kode_dok, nomor)
        VALUES (p_kode_divisi, p_kode_dok, v_nomor_baru);
    END IF;
    
    -- Return the new number
    p_nomor := v_nomor_baru;
END;
$$;

-- 12. PROCEDURE sp_tambah_saldo
CREATE OR REPLACE PROCEDURE sp_tambah_saldo(
    p_kode_divisi VARCHAR(5),
    p_no_rekening VARCHAR(50),
    p_keterangan VARCHAR(500),
    p_nominal NUMERIC(15,2)
)
LANGUAGE plpgsql
AS $$
DECLARE
    v_saldo NUMERIC(15,2);
BEGIN
    -- Get current balance
    SELECT COALESCE(saldo, 0) INTO v_saldo
    FROM d_bank 
    WHERE kode_divisi = p_kode_divisi AND no_rekening = p_no_rekening;
    
    v_saldo := v_saldo + p_nominal;
    
    -- Insert into saldo_bank
    INSERT INTO saldo_bank(
        kode_divisi, no_rekening, tgl_proses, keterangan,
        debet, kredit, saldo
    ) VALUES (
        p_kode_divisi, p_no_rekening, CURRENT_DATE, p_keterangan,
        p_nominal, 0, v_saldo
    );
    
    -- Update d_bank
    UPDATE d_bank SET saldo = v_saldo
    WHERE kode_divisi = p_kode_divisi AND no_rekening = p_no_rekening;
END;
$$;

-- 13. PROCEDURE sp_tanda_terima
CREATE OR REPLACE PROCEDURE sp_tanda_terima(
    p_no_tt VARCHAR(15),
    p_kode_cust VARCHAR(5),
    p_ket VARCHAR(500),
    p_no_ref VARCHAR(15)
)
LANGUAGE plpgsql
AS $$
BEGIN
    -- Check if TT exists
    IF NOT EXISTS (SELECT 1 FROM m_tt WHERE no_tt = p_no_tt) THEN
        -- Insert into m_tt
        INSERT INTO m_tt(no_tt, tanggal, kode_cust, keterangan)
        VALUES (p_no_tt, CURRENT_DATE, p_kode_cust, p_ket);
    END IF;
    
    -- Insert into d_tt
    INSERT INTO d_tt(no_tt, no_ref) VALUES (p_no_tt, p_no_ref);
    
    -- Update references
    UPDATE invoice SET tt = p_no_tt WHERE no_invoice = p_no_ref;
    UPDATE return_sales SET tt = p_no_tt WHERE no_retur = p_no_ref;
END;
$$;

-- 14. PROCEDURE sp_voucher
CREATE OR REPLACE PROCEDURE sp_voucher(
    p_no_voucher VARCHAR(15),
    p_kode_sales VARCHAR(5),
    p_total_omzet NUMERIC(15,2),
    p_komisi NUMERIC(5,2),
    p_jumlah_komisi NUMERIC(15,2),
    p_no_ref VARCHAR(15)
)
LANGUAGE plpgsql
AS $$
BEGIN
    -- Check if voucher exists
    IF NOT EXISTS (SELECT 1 FROM m_voucher WHERE no_voucher = p_no_voucher) THEN
        -- Insert into m_voucher
        INSERT INTO m_voucher(
            no_voucher, tanggal, kode_sales, total_omzet, komisi, jumlah_komisi
        ) VALUES (
            p_no_voucher, CURRENT_DATE, p_kode_sales, p_total_omzet, p_komisi, p_jumlah_komisi
        );
    END IF;
    
    -- Insert into d_voucher
    INSERT INTO d_voucher(no_voucher, no_penerimaan) VALUES (p_no_voucher, p_no_ref);
    
    -- Update penerimaan_finance
    UPDATE penerimaan_finance SET no_voucher = p_no_voucher WHERE no_penerimaan = p_no_ref;
END;
$$;