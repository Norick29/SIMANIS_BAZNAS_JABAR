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
        Schema::create('aset_hilang', function (Blueprint $table) {
            $table->bigIncrements('id_hilang');
            $table->bigInteger('id_aset')->unsigned();
            $table->date('tanggal_hilang');
            $table->text('kronologi');
            $table->bigInteger('requested_by')->unsigned()->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('id_aset')->references('id_aset')->on('assets')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aset_hilang');
    }
};
