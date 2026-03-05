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
        Schema::create('post_tag', function (Blueprint $table) {
            // Внешний ключ для постов
            $table->foreignId('post_id')->constrained('posts')->onDelete('cascade');

            // Внешний ключ для тегов
            $table->foreignId('tag_id')->constrained('tags')->onDelete('cascade');

            // Составной первичный ключ для уникальности пары
            $table->primary(['post_id', 'tag_id']);

            // Временные метки (created_at, updated_at)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post_tag');
    }
};
