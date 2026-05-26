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
        Schema::table('signatures', function (Blueprint $table) {
            $table->unsignedBigInteger('id_pindah');
            $table->foreign('id_pindah')->references('id_pindah')->on('pindah_asets')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('signatures', function (Blueprint $table) {
            $table->dropForeign(['id_pindah']);
            $table->dropColumn('id_pindah');
        });
    }
};
