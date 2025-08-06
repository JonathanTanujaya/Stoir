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
        Schema::create('m_resi', function (Blueprint $table) {
            $table->string('KodeDivisi', 5);
            $table->string('NoResi', 15);
            $table->string('NoRekeningTujuan', 50)->nullable();
            $table->string('TglPembayaran', 50)->nullable();
            $table->string('KodeCust', 5)->nullable();
            $table->decimal('Jumlah', 19, 4)->nullable();
            $table->decimal('SisaResi', 19, 4)->nullable();
            $table->text('Keterangan')->nullable();
            $table->string('Status', 50)->nullable();

            $table->primary(['KodeDivisi', 'NoResi']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_resi');
    }
};
