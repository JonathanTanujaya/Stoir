<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add foreign keys for m_bank
        Schema::table('m_bank', function (Blueprint $table) {
            $table->foreign('kode_divisi')->references('kode_divisi')->on('m_divisi');
        });

        // Add foreign keys for d_bank
        Schema::table('d_bank', function (Blueprint $table) {
            $table->foreign('kode_divisi')->references('kode_divisi')->on('m_divisi');
            $table->foreign(['kode_divisi', 'kode_bank'])->references(['kode_divisi', 'kode_bank'])->on('m_bank');
        });

        // Add foreign keys for m_area
        Schema::table('m_area', function (Blueprint $table) {
            $table->foreign('kode_divisi')->references('kode_divisi')->on('m_divisi');
        });

        // Add foreign keys for m_sales (add kode_area column first if not exists)
        if (!Schema::hasColumn('m_sales', 'kode_area')) {
            Schema::table('m_sales', function (Blueprint $table) {
                $table->string('kode_area', 5)->nullable();
            });
        }
        Schema::table('m_sales', function (Blueprint $table) {
            $table->foreign('kode_divisi')->references('kode_divisi')->on('m_divisi');
            $table->foreign(['kode_divisi', 'kode_area'])->references(['kode_divisi', 'kode_area'])->on('m_area');
        });

        // Add foreign keys for m_cust (add kode_area and kode_sales columns first if not exists)
        if (!Schema::hasColumn('m_cust', 'kode_area')) {
            Schema::table('m_cust', function (Blueprint $table) {
                $table->string('kode_area', 5)->nullable();
            });
        }
        if (!Schema::hasColumn('m_cust', 'kode_sales')) {
            Schema::table('m_cust', function (Blueprint $table) {
                $table->string('kode_sales', 10)->nullable();
            });
        }
        Schema::table('m_cust', function (Blueprint $table) {
            $table->foreign('kode_divisi')->references('kode_divisi')->on('m_divisi');
            $table->foreign(['kode_divisi', 'kode_area'])->references(['kode_divisi', 'kode_area'])->on('m_area');
        });

        // Add foreign keys for m_kategori (add kode_divisi column first if not exists)
        if (!Schema::hasColumn('m_kategori', 'kode_divisi')) {
            Schema::table('m_kategori', function (Blueprint $table) {
                $table->string('kode_divisi', 5);
            });
        }
        Schema::table('m_kategori', function (Blueprint $table) {
            $table->foreign('kode_divisi')->references('kode_divisi')->on('m_divisi');
        });

        // Add foreign keys for m_barang (add kode_divisi column first if not exists)
        if (!Schema::hasColumn('m_barang', 'kode_divisi')) {
            Schema::table('m_barang', function (Blueprint $table) {
                $table->string('kode_divisi', 5);
            });
        }
        Schema::table('m_barang', function (Blueprint $table) {
            $table->foreign('kode_divisi')->references('kode_divisi')->on('m_divisi');
            $table->foreign(['kode_divisi', 'kode_kategori'])->references(['kode_divisi', 'kode_kategori'])->on('m_kategori');
        });

        // Add foreign keys for d_barang
        Schema::table('d_barang', function (Blueprint $table) {
            $table->foreign('kode_divisi')->references('kode_divisi')->on('m_divisi');
            $table->foreign(['kode_divisi', 'kode_barang'])->references(['kode_divisi', 'kode_barang'])->on('m_barang');
        });

        // Add foreign keys for m_supplier
        Schema::table('m_supplier', function (Blueprint $table) {
            $table->foreign('kode_divisi')->references('kode_divisi')->on('m_divisi');
        });

        // Add foreign keys for invoice
        Schema::table('invoice', function (Blueprint $table) {
            $table->foreign('kode_divisi')->references('kode_divisi')->on('m_divisi');
            $table->foreign(['kode_divisi', 'kode_cust'])->references(['kode_divisi', 'kode_cust'])->on('m_cust');
            $table->foreign(['kode_divisi', 'kode_sales'])->references(['kode_divisi', 'kode_sales'])->on('m_sales');
        });

        // Add foreign keys for invoice_detail
        Schema::table('invoice_detail', function (Blueprint $table) {
            $table->foreign(['kode_divisi', 'no_invoice'])->references(['kode_divisi', 'no_invoice'])->on('invoice');
            $table->foreign(['kode_divisi', 'kode_barang'])->references(['kode_divisi', 'kode_barang'])->on('m_barang');
        });

        // Add foreign keys for kartu_stok
        Schema::table('kartu_stok', function (Blueprint $table) {
            $table->foreign('kode_divisi')->references('kode_divisi')->on('m_divisi');
            $table->foreign(['kode_divisi', 'kode_barang'])->references(['kode_divisi', 'kode_barang'])->on('m_barang');
        });

        // Add foreign keys for part_penerimaan
        Schema::table('part_penerimaan', function (Blueprint $table) {
            $table->foreign('kode_divisi')->references('kode_divisi')->on('m_divisi');
            $table->foreign(['kode_divisi', 'kode_supplier'])->references(['kode_divisi', 'kode_supplier'])->on('m_supplier');
        });

        // Add foreign keys for part_penerimaan_detail
        Schema::table('part_penerimaan_detail', function (Blueprint $table) {
            $table->foreign(['kode_divisi', 'no_penerimaan'])->references(['kode_divisi', 'no_penerimaan'])->on('part_penerimaan');
            $table->foreign(['kode_divisi', 'kode_barang'])->references(['kode_divisi', 'kode_barang'])->on('m_barang');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop foreign keys in reverse order
        Schema::table('part_penerimaan_detail', function (Blueprint $table) {
            $table->dropForeign(['kode_divisi', 'no_penerimaan']);
            $table->dropForeign(['kode_divisi', 'kode_barang']);
        });

        Schema::table('part_penerimaan', function (Blueprint $table) {
            $table->dropForeign(['kode_divisi']);
            $table->dropForeign(['kode_divisi', 'kode_supplier']);
        });

        Schema::table('kartu_stok', function (Blueprint $table) {
            $table->dropForeign(['kode_divisi']);
            $table->dropForeign(['kode_divisi', 'kode_barang']);
        });

        Schema::table('invoice_detail', function (Blueprint $table) {
            $table->dropForeign(['kode_divisi', 'no_invoice']);
            $table->dropForeign(['kode_divisi', 'kode_barang']);
        });

        Schema::table('invoice', function (Blueprint $table) {
            $table->dropForeign(['kode_divisi']);
            $table->dropForeign(['kode_divisi', 'kode_cust']);
            $table->dropForeign(['kode_divisi', 'kode_sales']);
        });

        Schema::table('m_supplier', function (Blueprint $table) {
            $table->dropForeign(['kode_divisi']);
        });

        Schema::table('d_barang', function (Blueprint $table) {
            $table->dropForeign(['kode_divisi']);
            $table->dropForeign(['kode_divisi', 'kode_barang']);
        });

        Schema::table('m_barang', function (Blueprint $table) {
            $table->dropForeign(['kode_divisi']);
            $table->dropForeign(['kode_divisi', 'kode_kategori']);
        });

        Schema::table('m_kategori', function (Blueprint $table) {
            $table->dropForeign(['kode_divisi']);
        });

        Schema::table('m_cust', function (Blueprint $table) {
            $table->dropForeign(['kode_divisi']);
            $table->dropForeign(['kode_divisi', 'kode_area']);
        });

        Schema::table('m_sales', function (Blueprint $table) {
            $table->dropForeign(['kode_divisi']);
            $table->dropForeign(['kode_divisi', 'kode_area']);
        });

        Schema::table('m_area', function (Blueprint $table) {
            $table->dropForeign(['kode_divisi']);
        });

        Schema::table('d_bank', function (Blueprint $table) {
            $table->dropForeign(['kode_divisi']);
            $table->dropForeign(['kode_divisi', 'kode_bank']);
        });

        Schema::table('m_bank', function (Blueprint $table) {
            $table->dropForeign(['kode_divisi']);
        });
    }
};
