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
        Schema::create('assets', function (Blueprint $table) {
            $table->id('id_aset');
            $table->string('kode_aset', 50)->unique();
            $table->unsignedBigInteger('id_barang');
            $table->unsignedBigInteger('id_kategori');
            $table->unsignedBigInteger('id_lokasi');
            $table->unsignedBigInteger('id_amil');
            $table->integer('volume')->default(1);
            $table->string('satuan', 20)->nullable();
            $table->double('harga')->default(0);
            $table->double('total_harga')->default(0);
            $table->enum('kondisi', ['baik', 'rusak', 'hilang'])->default('baik');
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->string('jenis_aset', 100)->nullable();
            $table->string('qr_code', 255)->nullable();
            $table->timestamps();
            $table->softDeletes();
                                                                                                
            $table->foreign('id_barang')->references('id_barang')->on('barangs')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('id_kategori')->references('id_kategori')->on('kategoris')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('id_lokasi')->references('id_lokasi')->on('lokasis')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('id_amil')->references('id_amil')->on('amils')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
