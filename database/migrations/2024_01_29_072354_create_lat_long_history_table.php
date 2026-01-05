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
        Schema::create('lat_long_history', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('arogyamitra_id');
            $table->string('type');
            $table->string('latitude');
            $table->string('longitude');
            $table->foreign('arogyamitra_id')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lat_long_history');
    }
};
