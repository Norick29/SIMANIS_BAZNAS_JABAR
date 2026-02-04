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
        Schema::create('pindah_asets', function (Blueprint $table) {
            $table->uuid('id_pindah')->primary();
            $table->unsignedBigInteger('id_aset');
            $table->unsignedBigInteger('id_amil_sebelum');
            $table->unsignedBigInteger('id_amil_sesudah');
            $table->unsignedBigInteger('id_lokasi_baru');
            $table->text('alasan')->nullable();
            $table->enum('status', ['menunggu', 'disetujui', 'ditolak'])->default('menunggu');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('id_aset')->references('id_aset')->on('assets')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('id_amil_sebelum')->references('id_amil')->on('amils')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('id_amil_sesudah')->references('id_amil')->on('amils')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('id_lokasi_baru')->references('id_lokasi')->on('lokasis')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pindah_asets');
    }
};
