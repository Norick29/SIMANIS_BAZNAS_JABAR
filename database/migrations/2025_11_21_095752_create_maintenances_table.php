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
        Schema::create('maintenances', function (Blueprint $table) {
            $table->id('id_maintenance');
            $table->unsignedBigInteger('id_aset');
            $table->string('jenis', 100);
            $table->text('deskripsi')->nullable();
            $table->string('durasi', 50)->nullable();
            $table->double('biaya')->default(0);
            $table->enum('status', ['proses', 'selesai'])->default('proses');
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('id_aset')->references('id_aset')->on('assets')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenances');
    }
};
