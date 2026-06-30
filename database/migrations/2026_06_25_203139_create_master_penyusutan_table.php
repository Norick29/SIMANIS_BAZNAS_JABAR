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
        Schema::create('master_penyusutan', function (Blueprint $table) {
            $table->id('id_master_penyusutan');

            $table->unsignedBigInteger('id_jenis');

            $table->integer('umur_ekonomis');

            $table->decimal('persentase',5,2);

            $table->enum('status',['aktif','nonaktif'])
                ->default('aktif');

            $table->timestamps();

            $table->foreign('id_jenis')
                ->references('id_jenis')
                ->on('jenis')   
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_penyusutan');
    }
};
