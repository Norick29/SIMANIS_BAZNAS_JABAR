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
        Schema::create('sumbers', function (Blueprint $table) {
            $table->id('id_sumber_dana');
            $table->string('kode_sumber_dana', 20)->unique();
            $table->string('nama_sumber_dana', 100);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sumbers');
    }
};
