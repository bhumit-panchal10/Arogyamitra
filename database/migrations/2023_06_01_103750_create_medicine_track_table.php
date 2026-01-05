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
        Schema::create('medicine_track', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('arogyamitra_id');
            $table->unsignedBigInteger('medicine_id');
            $table->string('qty');
            $table->enum('mode', ['R', 'C'])->comment('r: received, c: consumed');
            $table->timestamps();
            $table->foreign('medicine_id')->references('id')->on('medicine')->onDelete('cascade');
            $table->foreign('arogyamitra_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicine_track');
    }
};
