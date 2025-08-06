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
        Schema::create('m_voucher', function (Blueprint $table) {
            $table->string('NoVoucher', 15);
            $table->date('Tanggal')->nullable();
            $table->string('KodeSales', 5)->nullable();
            $table->decimal('TotalOmzet', 19, 4)->nullable();
            $table->float('Komisi')->nullable();
            $table->decimal('JumlahKomisi', 19, 4)->nullable();

            $table->primary(['NoVoucher']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_voucher');
    }
};
