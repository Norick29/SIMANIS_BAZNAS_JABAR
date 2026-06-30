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
        Schema::table('pindah_asets', function (Blueprint $table) {
            $table->unsignedBigInteger('id_lokasi_lama')->nullable()->after('id_aset');
        $table->foreign('id_lokasi_lama')->references('id_lokasi')->on('lokasis')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pindah_asets', function (Blueprint $table) {
            //
        });
    }
};
