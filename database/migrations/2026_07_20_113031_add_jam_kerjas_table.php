<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jam_kerjas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profile_id')->constrained()->cascadeOnDelete();
            $table->string('hari'); // Senin, Selasa, Rabu, Kamis, Jumat, Sabtu, Minggu
            $table->time('jam_buka')->nullable();
            $table->time('jam_tutup')->nullable();
            $table->boolean('is_libur')->default(false);
            $table->timestamps();

            $table->unique(['profile_id', 'hari']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jam_kerjas');
    }
};
