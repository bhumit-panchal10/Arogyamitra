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
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->nullable();
            $table->string('address')->nullable();
            $table->unsignedBigInteger('gram_id')->nullable();
            $table->unsignedBigInteger('jilla_id')->nullable();
            $table->unsignedBigInteger('vibhag_id')->nullable();
            $table->string('mobile_no');
            $table->enum('status', ['Active', 'Deactive']);
            $table->tinyInteger('role')->comment('1 for backend, 2 for app user,3 for arogyamitra,4 for vibhag');
            $table->foreign('gram_id')->references('id')->on('gram')->onDelete('cascade');
            $table->foreign('jilla_id')->references('id')->on('jilla')->onDelete('cascade');
            $table->foreign('vibhag_id')->references('id')->on('vibhag')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('username');
            $table->dropColumn('address');
            $table->dropColumn('gram_id');
            $table->dropColumn('jilla_id');
            $table->dropColumn('vibhag_id');
            $table->dropColumn('mobile_no');
            $table->dropColumn('status', ['Active', 'Deactive']);
            $table->dropColumn('role')->comment('1 for backend, 2 for app user,3 for arogyamitra,4 for vibhag users');
            $table->dropForeign(['gram_id', 'jilla_id', 'vibhag_id']);
        });
    }
};
