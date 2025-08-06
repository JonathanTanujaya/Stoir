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
        Schema::create('invoice_bonus', function (Blueprint $table) {
            $table->string('KodeDivisi', 4);
            $table->string('NoInvoiceBonus', 15);
            $table->date('TglFaktur')->nullable();
            $table->string('KodeCust', 5)->nullable();
            $table->string('Ket', 500)->nullable();
            $table->string('Status', 50)->nullable();
            $table->string('username', 50)->nullable();

            $table->primary(['KodeDivisi', 'NoInvoiceBonus']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_bonus');
    }
};
