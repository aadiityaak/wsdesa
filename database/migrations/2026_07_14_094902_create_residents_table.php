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
        Schema::create('residents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('family_id')->nullable()->constrained()->nullOnDelete();
            $table->string('nik', 16)->unique();
            $table->string('nama', 200);
            $table->string('tempat_lahir', 100)->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->enum('agama', ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu', 'Lainnya']);
            $table->enum('status_perkawinan', ['Belum Kawin', 'Kawin', 'Cerai Hidup', 'Cerai Mati']);
            $table->string('pekerjaan', 100)->nullable();
            $table->enum('pendidikan', ['Tidak Sekolah', 'SD', 'SMP', 'SMA', 'D3', 'S1', 'S2', 'S3']);
            $table->enum('gol_darah', ['A', 'B', 'AB', 'O'])->nullable();
            $table->string('dusun', 100)->nullable();
            $table->string('rt', 5)->nullable();
            $table->string('rw', 5)->nullable();
            $table->enum('status', ['Tetap', 'Lahir', 'Mati', 'Pindah'])->default('Tetap');
            $table->enum('hubungan_keluarga', ['Kepala Keluarga', 'Istri', 'Anak', 'Orang Tua', 'Menantu', 'Cucu', 'Famili Lain'])->nullable();
            $table->string('foto')->nullable();
            $table->string('telepon', 20)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('residents');
    }
};
