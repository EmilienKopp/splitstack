<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('known_issues', function (Blueprint $table) {
            $table->id();
            $table->string('jira_id')->nullable();
            $table->string('jira_key')->unique();
            $table->text('summary');
            $table->text('description')->nullable();
            $table->string('status');
            $table->string('status_category')->nullable();
            $table->string('status_category_name')->nullable();
            $table->string('status_color')->nullable();
            $table->string('priority')->nullable();
            $table->text('priority_icon_url')->nullable();
            $table->string('issue_type')->nullable();
            $table->text('issue_type_icon_url')->nullable();
            $table->timestamp('first_reported_at')->nullable();
            $table->timestamp('last_updated_at')->nullable();
            $table->timestamp('current_status_since')->nullable();
            $table->jsonb('metadata')->nullable();
            $table->boolean('is_deployed')->default(false);
            $table->jsonb('deployment_metadata')->nullable();
            $table->timestamp('deployed_at')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('status_category');
            $table->index('priority');
            $table->index('first_reported_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('known_issues');
    }
};
