<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Migration for testing only - create minimal tables needed for tests
        
        // Master Divisi
        Schema::create('m_divisi', function (Blueprint $table) {
            $table->string('kode_divisi')->primary();
            $table->string('nama_divisi');
        });

        // Master User
        Schema::create('master_user', function (Blueprint $table) {
            $table->string('kode_divisi');
            $table->string('username');
            $table->string('nama');
            $table->string('password');
            $table->primary(['kode_divisi', 'username']);
        });

        // Master Customer
        Schema::create('m_cust', function (Blueprint $table) {
            $table->string('kode_divisi');
            $table->string('kode_cust');
            $table->string('nama_cust');
            $table->string('kode_area')->nullable();
            $table->string('alamat')->nullable();
            $table->string('telp')->nullable();
            $table->string('contact')->nullable();
            $table->decimal('credit_limit', 15, 2)->default(0);
            $table->integer('jatuh_tempo')->default(30);
            $table->boolean('status')->default(true);
            $table->string('no_npwp')->nullable();
            $table->string('nama_pajak')->nullable();
            $table->string('alamat_pajak')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->primary(['kode_divisi', 'kode_cust']);
        });

        // Master Sales
        Schema::create('m_sales', function (Blueprint $table) {
            $table->string('kode_divisi');
            $table->string('kode_sales');
            $table->string('nama_sales');
            $table->string('kode_area')->nullable();
            $table->string('alamat')->nullable();
            $table->string('no_hp')->nullable();
            $table->decimal('target', 15, 2)->default(0);
            $table->boolean('status')->default(true);
            $table->primary(['kode_divisi', 'kode_sales']);
        });

        // Master Area
        Schema::create('m_area', function (Blueprint $table) {
            $table->string('kode_divisi');
            $table->string('kode_area');
            $table->string('area');
            $table->boolean('status')->default(true);
            $table->primary(['kode_divisi', 'kode_area']);
        });

        // Master Kategori
        Schema::create('m_kategori', function (Blueprint $table) {
            $table->string('kode_divisi');
            $table->string('kode_kategori');
            $table->string('kategori');
            $table->boolean('status')->default(true);
            $table->primary(['kode_divisi', 'kode_kategori']);
        });

        // Master Barang
        Schema::create('m_barang', function (Blueprint $table) {
            $table->string('kode_divisi');
            $table->string('kode_barang');
            $table->string('nama_barang');
            $table->string('kode_kategori');
            $table->decimal('harga_list', 15, 2)->default(0);
            $table->decimal('harga_jual', 15, 2)->default(0);
            $table->string('satuan')->nullable();
            $table->decimal('disc1', 5, 2)->default(0);
            $table->decimal('disc2', 5, 2)->default(0);
            $table->string('merk')->nullable();
            $table->string('barcode')->nullable();
            $table->boolean('status')->default(true);
            $table->string('lokasi')->nullable();
            $table->integer('stok_min')->default(0);
            $table->timestamps();
            $table->primary(['kode_divisi', 'kode_barang']);
        });

        // Detail Barang (Stok)
        Schema::create('d_barang', function (Blueprint $table) {
            $table->id();
            $table->string('kode_divisi');
            $table->string('kode_barang');
            $table->timestamp('tgl_masuk');
            $table->decimal('modal', 15, 2);
            $table->integer('stok');
        });

        // Invoice
        Schema::create('invoice', function (Blueprint $table) {
            $table->string('kode_divisi');
            $table->string('no_invoice');
            $table->date('tgl_faktur');
            $table->string('kode_cust');
            $table->string('kode_sales');
            $table->string('tipe');
            $table->date('jatuh_tempo');
            $table->decimal('total', 15, 2);
            $table->decimal('disc', 15, 2);
            $table->decimal('pajak', 15, 2);
            $table->decimal('grand_total', 15, 2);
            $table->decimal('sisa_invoice', 15, 2);
            $table->text('ket')->nullable();
            $table->string('status');
            $table->string('username');
            $table->string('tt')->nullable();
            $table->timestamps();
            $table->primary(['kode_divisi', 'no_invoice']);
        });

        // Invoice Detail
        Schema::create('invoice_detail', function (Blueprint $table) {
            $table->id();
            $table->string('kode_divisi');
            $table->string('no_invoice');
            $table->string('kode_barang');
            $table->integer('qty_supply');
            $table->decimal('harga_jual', 15, 2);
            $table->string('jenis')->nullable();
            $table->decimal('diskon1', 5, 2)->default(0);
            $table->decimal('diskon2', 5, 2)->default(0);
            $table->decimal('harga_nett', 15, 2);
            $table->string('status');
        });

        // Opname
        Schema::create('opname', function (Blueprint $table) {
            $table->string('kode_divisi');
            $table->string('no_opname');
            $table->date('tanggal');
            $table->decimal('total', 15, 2);
            $table->primary(['kode_divisi', 'no_opname']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_detail');
        Schema::dropIfExists('invoice');
        Schema::dropIfExists('d_barang');
        Schema::dropIfExists('m_barang');
        Schema::dropIfExists('m_kategori');
        Schema::dropIfExists('m_area');
        Schema::dropIfExists('m_sales');
        Schema::dropIfExists('m_cust');
        Schema::dropIfExists('master_user');
        Schema::dropIfExists('m_divisi');
        Schema::dropIfExists('opname');
    }
};
