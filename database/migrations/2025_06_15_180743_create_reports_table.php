<?php

use App\Enums\ReportTypes;
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

        Schema::create('github_connections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('github_user_id');
            $table->string('username')->unique();
            $table->string('access_token')->nullable()
                ->comment('GitHub access token for API access');
            $table->string('refresh_token')->nullable()
                ->comment('GitHub refresh token for API access');
            $table->string('account_handle')->nullable()
                ->comment('GitHub account handle for display purposes');
            $table->string('avatar_url')->nullable()
                ->comment('URL to the user\'s GitHub avatar image');
            $table->timestamp('token_expires_at')->nullable()
                ->comment('Expiration time for the access token');
            $table->timestamps();
        });

        Schema::create('repositories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('github_connection_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('url')->unique();
            $table->timestamps();
        });

        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('repository_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->boolean('is_default')->default(false)
                ->comment('Indicates if this branch is the default branch');
            $table->timestamps();
        });

        Schema::create('repository_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('repository_id')->constrained()->onDelete('cascade');
            $table->foreignId('branch_id')->nullable()->constrained()->onDelete('cascade');
            $table->jsonb('excluded_folders')->nullable()->default(json_encode([]));
            $table->jsonb('excluded_file_extensions')->nullable()->default(json_encode([]));
            $table->timestamps();
        });

        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('project_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('organization_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('repository_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('branch_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('title')->nullable();
            $table->text('content')->nullable();
            $table->string('report_type')->default(ReportTypes::TASK_BASED->value)
                ->comment('Type of report, e.g., technical, financial, operational, task_based');
            $table->text('original_log')->nullable();
            $table->text('aggregated_diff')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
        Schema::dropIfExists('github_connections');
        Schema::dropIfExists('branches');
        Schema::dropIfExists('repositories');
    }
};
