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
        Schema::table('medicine_stock', function (Blueprint $table) {
            $table->unsignedBigInteger('gram_id')->nullable();
            $table->foreign('gram_id')->references('id')->on('gram');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medicine_stock', function (Blueprint $table) {
            $table->dropForeign(['gram_id']);
            $table->dropColumn('gram_id');
        });
    }
};
