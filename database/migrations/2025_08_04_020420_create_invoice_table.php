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
        Schema::create('invoice', function (Blueprint $table) {
            $table->string('KodeDivisi', 4);
            $table->string('NoInvoice', 15);
            $table->date('TglFaktur')->nullable();
            $table->string('KodeCust', 5)->nullable();
            $table->string('KodeSales', 5)->nullable();
            $table->char('Tipe', 1)->nullable();
            $table->date('JatuhTempo')->nullable();
            $table->decimal('Total', 19, 4)->nullable();
            $table->float('Disc')->nullable();
            $table->float('Pajak')->nullable();
            $table->decimal('GrandTotal', 19, 4)->nullable();
            $table->decimal('SisaInvoice', 19, 4)->nullable();
            $table->string('Ket', 500)->nullable();
            $table->string('Status', 50)->nullable();
            $table->string('username', 50)->nullable();
            $table->string('TT', 15)->nullable();

            $table->primary(['KodeDivisi', 'NoInvoice']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice');
    }
};
