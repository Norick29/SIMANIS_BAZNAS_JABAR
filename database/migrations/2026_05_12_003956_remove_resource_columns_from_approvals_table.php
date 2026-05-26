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
             $table->dropColumn([
            'resource_type',
            'resource_id'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('approvals', function (Blueprint $table) {
            $table->string('resource_type', 50);
            $table->unsignedBigInteger('resource_id');
        });
    }
};
