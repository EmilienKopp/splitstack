<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('domain')->nullable()->unique();
            $table->string('host')->unique();
            $table->string('database')->unique();
            $table->string('org_id')->unique();
            $table->string('hash')->nullable();
            $table->jsonb('n8n_webhooks')->nullable();
            $table->jsonb('n8n_config')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
