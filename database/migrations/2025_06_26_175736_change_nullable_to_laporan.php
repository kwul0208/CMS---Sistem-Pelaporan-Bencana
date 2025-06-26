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
        Schema::table('laporan', function (Blueprint $table) {
            $table->string('photo_1')->nullable()->change();
            $table->string('photo_2')->nullable()->change();
            $table->string('photo_3')->nullable()->change();
            $table->string('photo_4')->nullable()->change();
            $table->string('photo_5')->nullable()->change();
            $table->string('video')->nullable()->change();
            $table->string('address')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('laporan', function (Blueprint $table) {
            $table->string('photo_1')->change();
            $table->string('photo_2')->change();
            $table->string('photo_3')->change();
            $table->string('photo_4')->change();
            $table->string('photo_5')->change();
            $table->string('video')->change();
            $table->string('address')->change();
        });
    }
};

