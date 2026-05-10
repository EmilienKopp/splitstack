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
        Schema::create('task_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->timestamps();
        });

        Schema::create('task_category_aliases', function (Blueprint $table) {
            $table->id();
            $table->string('alias');
            $table->foreignId('task_category_id')->constrained()->onDelete('cascade');
            $table->foreignId('organization_id')->nullable()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->nullOnDelete();
            $table->timestamps();

            $table->unique(['task_category_id', 'organization_id', 'user_id']);
            $table->index('alias');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_category_aliases');
        Schema::dropIfExists('task_categories');
    }
};
