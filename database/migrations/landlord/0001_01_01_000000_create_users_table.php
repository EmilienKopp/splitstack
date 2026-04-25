<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('workos_id')->unique()->nullable();
            $table->string('email')->unique();
            $table->string('handle')->unique();
            $table->text('first_name');
            $table->text('middle_name')->nullable();
            $table->text('last_name');
            $table->text('title')->nullable();
            $table->text('phone_main')->nullable();
            $table->text('phone_secondary')->nullable();
            $table->text('bio')->nullable();
            $table->text('avatar')->nullable();
            $table->jsonb('role_data')->nullable();
            $table->text('facebook')->nullable();
            $table->text('x_twitter')->nullable();
            $table->text('instagram')->nullable();
            $table->text('linkedin')->nullable();
            $table->text('youtube')->nullable();
            $table->text('website')->nullable();
            $table->text('github')->nullable();
            $table->jsonb('dashboard_preferences')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
