<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('known_issue_status_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('known_issue_id')->constrained('known_issues')->onDelete('cascade');
            $table->string('status');
            $table->string('status_category')->nullable();
            $table->string('status_category_name')->nullable();
            $table->timestamp('changed_at');
            $table->integer('duration_seconds')->nullable();
            $table->timestamps();

            $table->index('known_issue_id');
            $table->index('changed_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('known_issue_status_history');
    }
};
