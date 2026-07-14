<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('developments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('development_category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('nama');
            $table->string('slug')->unique();
            $table->string('lokasi')->nullable();
            $table->text('deskripsi');
            $table->decimal('anggaran', 15, 2)->nullable();
            $table->string('sumber_dana')->nullable();
            $table->integer('tahun')->nullable();
            $table->enum('status', ['rencana', 'berjalan', 'selesai']);
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('developments');
    }
};
