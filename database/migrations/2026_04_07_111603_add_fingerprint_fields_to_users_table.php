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
        Schema::table('users', function (Blueprint $table) {
            // Поля для идентификации пользователя по слепку
            $table->string('ip_address')->nullable()->after('password');
            $table->text('user_agent')->nullable()->after('ip_address');
            $table->string('geolocation')->nullable()->after('user_agent');
            $table->string('fingerprint_hash')->nullable()->unique()->after('geolocation');

            // Временные метки активности
            $table->timestamp('first_feedback_at')->nullable()->after('remember_token');
            $table->timestamp('last_feedback_at')->nullable()->after('first_feedback_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'ip_address',
                'user_agent',
                'geolocation',
                'fingerprint_hash',
                'first_feedback_at',
                'last_feedback_at'
            ]);
        });
    }
};
