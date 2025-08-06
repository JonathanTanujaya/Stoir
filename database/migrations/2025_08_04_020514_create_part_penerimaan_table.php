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
        Schema::create('part_penerimaan', function (Blueprint $table) {
            $table->string('KodeDivisi', 4);
            $table->string('NoPenerimaan', 15);
            $table->date('TglPenerimaan')->nullable();
            $table->string('KodeValas', 3)->nullable();
            $table->decimal('Kurs', 19, 4)->nullable();
            $table->string('KodeSupplier', 5)->nullable();
            $table->date('JatuhTempo')->nullable();
            $table->string('NoFaktur', 50)->nullable();
            $table->decimal('Total', 19, 4)->nullable();
            $table->float('Discount')->nullable();
            $table->float('Pajak')->nullable();
            $table->decimal('GrandTotal', 19, 4)->nullable();
            $table->string('Status', 50)->nullable();

            $table->primary(['KodeDivisi', 'NoPenerimaan']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('part_penerimaan');
    }
};
