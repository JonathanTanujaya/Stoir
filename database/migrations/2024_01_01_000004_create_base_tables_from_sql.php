<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $sql = file_get_contents(base_path('TABLES.sql'));
        DB::unprepared($sql);
    }

    public function down(): void
    {
        // Define how to reverse the migrations if needed
        // For simplicity, we can list tables to be dropped.
        // This part might need manual adjustment based on the tables in TABLES.sql
        $tables = [
            'company', 'm_coa', 'm_divisi', 'm_bank', 'd_bank', 'm_area', 'm_sales',
            'm_cust', 'm_kategori', 'm_barang', 'd_barang', 'm_supplier', 'm_trans',
            'd_trans', 'invoice', 'invoice_detail', 'journal', 'kartu_stok',
            'part_penerimaan', 'part_penerimaan_detail', 'm_tt', 'd_tt', 'm_voucher',
            'd_voucher', 'master_user', 'saldo_bank', 'return_sales', 'return_sales_detail',
            'retur_penerimaan', 'retur_penerimaan_detail', 'm_resi', 'penerimaan_finance',
            'penerimaan_finance_detail', 'm_dokumen', 'd_paket', 'stok_minimum'
        ];

        foreach (array_reverse($tables) as $table) {
            DB::statement("DROP TABLE IF EXISTS {$table} CASCADE");
        }
    }
};
