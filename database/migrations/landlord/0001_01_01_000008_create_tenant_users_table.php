<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenant_users', function (Blueprint $table) {
            $table->id();
            $table->uuid('tenant_id')->index();
            $table->unsignedBigInteger('user_id')->index();
            $table->string('email')->index();
            $table->string('github_user_id')->nullable()->index();
            $table->string('google_user_id')->nullable()->index();
            $table->timestamps();

            $table->unique(['tenant_id', 'user_id']);

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenant_users');
    }
};
