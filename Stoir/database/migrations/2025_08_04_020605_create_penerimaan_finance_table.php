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
        Schema::create('penerimaan_finance', function (Blueprint $table) {
            $table->string('KodeDivisi', 5);
            $table->string('NoPenerimaan', 15);
            $table->date('TglPenerimaan')->nullable();
            $table->string('Tipe', 50)->nullable();
            $table->string('NoRef', 50)->nullable();
            $table->date('TglRef')->nullable();
            $table->date('TglPencairan')->nullable();
            $table->string('BankRef', 5)->nullable();
            $table->string('NoRekTujuan', 50)->nullable();
            $table->string('KodeCust', 5)->nullable();
            $table->decimal('Jumlah', 19, 4)->nullable();
            $table->string('Status', 50)->nullable();
            $table->string('NoVoucher', 15)->nullable();

            $table->primary(['KodeDivisi', 'NoPenerimaan']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penerimaan_finance');
    }
};
