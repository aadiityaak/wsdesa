<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->dropColumn(['jam_buka', 'jam_tutup', 'jam_istirahat_mulai', 'jam_istirahat_selesai', 'hari_kerja']);
        });
    }

    public function down(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->string('jam_buka')->nullable()->after('website');
            $table->string('jam_tutup')->nullable()->after('jam_buka');
            $table->string('jam_istirahat_mulai')->nullable()->after('jam_tutup');
            $table->string('jam_istirahat_selesai')->nullable()->after('jam_istirahat_mulai');
            $table->string('hari_kerja')->nullable()->after('jam_istirahat_selesai');
        });
    }
};
