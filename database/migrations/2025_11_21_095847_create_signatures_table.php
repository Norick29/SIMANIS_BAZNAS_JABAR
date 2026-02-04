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
        Schema::create('signatures', function (Blueprint $table) {
            $table->id('id_signature');
            $table->unsignedBigInteger('id_aset');
            $table->unsignedBigInteger('user_id');
            $table->text('signature');
            $table->string('public_key_fingerprint', 128)->nullable();
            $table->string('algorithm', 64)->default('RSA-2048-SHA256');
            $table->timestamp('signed_at')->useCurrent();
            $table->timestamps();

            $table->foreign('user_id')->references('id_user')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('id_aset')->references('id_aset')->on('assets')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('signatures');
    }
};
