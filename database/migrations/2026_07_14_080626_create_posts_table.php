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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('judul');
            $table->string('slug')->unique();
            $table->string('thumbnail')->nullable();
            $table->text('ringkasan')->nullable();
            $table->longText('konten');
            $table->enum('status', ['draft', 'publish', 'archive'])->default('draft');
            $table->integer('views')->default(0);
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
