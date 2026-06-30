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
        Schema::table('approvals', function (Blueprint $table) {
            // Tambah kolom untuk polymorphic setelah kolom id_pindah
            $table->string('approval_type', 50)->nullable()->after('id_pindah');
            $table->bigInteger('reference_id')->unsigned()->nullable()->after('approval_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('approvals', function (Blueprint $table) {
            $table->bigInteger('id_pindah')->unsigned()->nullable(false)->change();
            $table->dropColumn(['approval_type', 'reference_id']);
        });
    }
};
