<?php

use App\Models\Project;
use App\Models\Repository;
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
        Schema::create('git_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Repository::class)->constrained()->cascadeOnDelete();
            $table->string('commit_hash')->nullable();
            $table->string('author_name')->nullable();
            $table->string('author_email')->nullable();
            $table->timestamp('committed_at')->nullable();
            $table->text('message');
            $table->text('diff')->nullable();
            $table->timestamps();
        });

        Schema::create('monitored_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Project::class);
            $table->string('process');
            $table->string('window_title')->nullable();
            $table->timestamp('timestamp');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('git_logs');
        Schema::dropIfExists('monitored_activities');
    }
};
