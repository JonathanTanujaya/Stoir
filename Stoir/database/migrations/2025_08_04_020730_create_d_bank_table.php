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
        Schema::create('d_bank', function (Blueprint $table) {
            $table->string('KodeDivisi', 5);
            $table->string('NoRekening', 50);
            $table->string('KodeBank', 5)->nullable();
            $table->string('AtasNama', 50)->nullable();
            $table->decimal('Saldo', 19, 4)->nullable();
            $table->string('Status', 50)->nullable();

            $table->primary(['KodeDivisi', 'NoRekening']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('d_bank');
    }
};
