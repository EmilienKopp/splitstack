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
        Schema::create('activity_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('color')->nullable();
            $table->string('icon')->nullable();
            $table->timestamps();
        });

        Schema::create('activity_type_aliases', function (Blueprint $table) {
            $table->id();
            $table->string('alias');
            $table->foreignId('activity_type_id')->constrained()->onDelete('cascade');
            $table->foreignId('organization_id')->nullable()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->nullOnDelete();
            $table->timestamps();

            $table->unique(['activity_type_id', 'organization_id', 'user_id']);
            $table->index('alias');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_categories');
    }
};
