<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('umkms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('umkm_category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('nama_usaha');
            $table->string('slug')->unique();
            $table->string('pemilik');
            $table->text('alamat')->nullable();
            $table->string('telepon')->nullable();
            $table->text('deskripsi')->nullable();
            $table->string('thumbnail')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('umkms');
    }
};
