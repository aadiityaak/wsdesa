<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('potentials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('potential_category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('nama');
            $table->string('slug')->unique();
            $table->text('deskripsi');
            $table->string('thumbnail')->nullable();
            $table->string('lokasi')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('potentials');
    }
};
