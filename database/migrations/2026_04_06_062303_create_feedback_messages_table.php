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
        Schema::create('feedback_messages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('contactInfo', 255);
            $table->enum('contact_type', ['email', 'phone']);
            $table->string('topic', 255)->nullable();
            $table->text('message');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->text('referrer')->nullable();
            $table->foreignId('user_id')->nullable();
            $table->boolean('is_read')->default(false);
            $table->boolean('is_spam')->default(false);
            $table->string('spam_reason', 255)->nullable();
            $table->timestamps();

            // Индексы для оптимизации запросов
            $table->index('contactInfo');
            $table->index('created_at');
            $table->index('is_read');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedback_messages');
    }
};
