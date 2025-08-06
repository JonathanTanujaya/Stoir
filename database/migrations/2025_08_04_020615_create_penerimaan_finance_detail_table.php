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
        Schema::create('penerimaan_finance_detail', function (Blueprint $table) {
            $table->string('KodeDivisi', 5);
            $table->string('NoPenerimaan', 15);
            $table->string('Noinvoice', 15)->nullable();
            $table->decimal('JumlahInvoice', 19, 4)->nullable();
            $table->decimal('SisaInvoice', 19, 4)->nullable();
            $table->decimal('JumlahBayar', 19, 4)->nullable();
            $table->decimal('JumlahDispensasi', 19, 4)->nullable();
            $table->string('Status', 50)->nullable();
            $table->bigIncrements('id');

            $table->primary(['KodeDivisi', 'NoPenerimaan']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penerimaan_finance_detail');
    }
};
