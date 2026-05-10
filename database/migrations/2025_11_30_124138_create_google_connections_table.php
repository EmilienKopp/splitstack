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
        Schema::create('google_connections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('google_user_id')->unique();
            $table->string('email');
            $table->string('access_token')->nullable()
                ->comment('Google access token for API access');
            $table->string('refresh_token')->nullable()
                ->comment('Google refresh token for API access');
            $table->timestamp('token_expires_at')->nullable()
                ->comment('Expiration time for the access token');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('google_connections');
    }
};
