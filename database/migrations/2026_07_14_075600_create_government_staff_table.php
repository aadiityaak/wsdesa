<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('government_staff', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('nip')->nullable();
            $table->string('jabatan');
            $table->string('foto')->nullable();
            $table->text('alamat')->nullable();
            $table->string('telepon')->nullable();
            $table->string('pendidikan_terakhir')->nullable();
            $table->date('tanggal_mulai_jabatan')->nullable();
            $table->date('tanggal_akhir_jabatan')->nullable();
            $table->integer('urutan')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('government_staff');
    }
};
