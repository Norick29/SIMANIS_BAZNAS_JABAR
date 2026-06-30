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
        Schema::create('penyusutan_aset', function (Blueprint $table) {
            $table->bigIncrements('id_penyusutan');
            $table->bigInteger('id_aset')->unsigned();
            $table->bigInteger('nilai_awal');
            $table->bigInteger('nilai_penyusutan');
            $table->bigInteger('nilai_setelah_penyusutan');
            $table->text('alasan');
            $table->enum('status', ['menunggu', 'disetujui', 'ditolak'])->default('menunggu');
            $table->bigInteger('requested_by')->unsigned()->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign Key ke tabel assets (sesuaikan nama tabel indukmu, di model kamu tertulis 'assets')
            $table->foreign('id_aset')->references('id_aset')->on('assets')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penyusutan_aset');
    }
};
