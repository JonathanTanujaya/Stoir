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
        Schema::create('m_cust', function (Blueprint $table) {
            $table->string('KodeDivisi', 4);
            $table->string('KodeCust', 5);
            $table->string('NamaCust', 200)->nullable();
            $table->string('KodeArea', 5)->nullable();
            $table->string('Alamat', 500)->nullable();
            $table->string('Telp', 50)->nullable();
            $table->string('Contact', 50)->nullable();
            $table->decimal('CreditLimit', 19, 4)->nullable();
            $table->integer('JatuhTempo')->nullable();
            $table->boolean('Status')->nullable();
            $table->string('NoNPWP', 20)->nullable();
            $table->string('NIK', 16)->nullable();
            $table->string('NamaPajak', 100)->nullable();
            $table->string('AlamatPajak', 500)->nullable();

            $table->primary(['KodeDivisi', 'KodeCust']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_cust');
    }
};
