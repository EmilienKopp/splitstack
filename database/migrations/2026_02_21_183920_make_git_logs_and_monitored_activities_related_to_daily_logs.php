<?php

use App\Models\DailyLog;
use App\Models\Project;
use App\Models\User;
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
        Schema::table('git_logs', function (Blueprint $table) {
            $table->foreignIdFor(DailyLog::class);
        });

        Schema::table('monitored_activities', function (Blueprint $table) {
            $table->dropForeignIdFor(User::class);
            $table->dropForeignIdFor(Project::class);
            $table->foreignIdFor(DailyLog::class);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('git_logs', function (Blueprint $table) {
            $table->dropForeignIdFor(DailyLog::class);
            $table->foreignIdFor(Project::class)->nullable();
            $table->foreignIdFor(User::class)->nullable();
        });
    }
};
