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
        Schema::table('vibhag', function (Blueprint $table) {
            $table->unsignedBigInteger('prant_id')->nullable()->after('name');
            $table->foreign('prant_id')->references('id')->on('prant')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vibhag', function (Blueprint $table) {
            $table->dropForeign('prant_id');
            $table->dropColumn('prant_id');
        });
    }
};
