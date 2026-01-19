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
        Schema::create('amils', function (Blueprint $table) {
            $table->id('id_amil');
            $table->string('nia', 20)->unique();
            $table->string('nama', 100);
            $table->unsignedBigInteger('id_jabatan');
            $table->unsignedBigInteger('id_divisi');
            $table->string('nama_dept', 100);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('id_jabatan')->references('id_jabatan')->on('jabatans')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('id_divisi')->references('id_divisi')->on('divisis')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('amils');
    }
};
