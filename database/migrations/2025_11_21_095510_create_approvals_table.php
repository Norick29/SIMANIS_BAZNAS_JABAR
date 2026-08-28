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
        Schema::create('approvals', function (Blueprint $table) {
            $table->id('id_approval');
            $table->unsignedBigInteger('id_pindah');
            $table->string('approval_type', 50)->nullable();
            $table->bigInteger('reference_id')->unsigned()->nullable();
            $table->enum('status', ['menunggu', 'disetujui', 'ditolak'])->default('menunggu');
            $table->unsignedBigInteger('requested_by')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->integer('stage')->default(1);
            $table->timestamp('approved_at')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
            $table->softDeletes();
            

            $table->foreign('id_pindah')->references('id_pindah')->on('pindah_asets')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('requested_by')->references('id_user')->on('users')->onUpdate('cascade')->onDelete('set null');
            $table->foreign('approved_by')->references('id_user')->on('users')->onUpdate('cascade')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approvals');
    }
};
