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
        Schema::table('profiles', function (Blueprint $table) {
            $table->string('kecamatan')->nullable()->change();
            $table->string('kabupaten')->nullable()->change();
            $table->string('provinsi')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->string('kecamatan')->nullable(false)->change();
            $table->string('kabupaten')->nullable(false)->change();
            $table->string('provinsi')->nullable(false)->change();
        });
    }
};
