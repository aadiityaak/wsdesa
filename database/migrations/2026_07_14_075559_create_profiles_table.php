<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->string('nama_desa');
            $table->string('kode_desa')->nullable();
            $table->string('kecamatan');
            $table->string('kabupaten');
            $table->string('provinsi');
            $table->string('alamat')->nullable();
            $table->string('kode_pos')->nullable();
            $table->string('telepon')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->string('logo')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->text('visi')->nullable();
            $table->text('misi')->nullable();
            $table->text('sejarah')->nullable();
            $table->string('luas_wilayah')->nullable();
            $table->text('batas_utara')->nullable();
            $table->text('batas_selatan')->nullable();
            $table->text('batas_timur')->nullable();
            $table->text('batas_barat')->nullable();
            $table->string('orbitasi_ke_kecamatan')->nullable();
            $table->string('orbitasi_ke_kabupaten')->nullable();
            $table->string('facebook')->nullable();
            $table->string('instagram')->nullable();
            $table->string('youtube')->nullable();
            $table->string('tiktok')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
