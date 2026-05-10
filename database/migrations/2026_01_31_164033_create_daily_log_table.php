<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('DROP VIEW IF EXISTS daily_logs_view');

        Schema::create('daily_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(\App\Models\User::class)->constrained()->onDelete('cascade');
            $table->foreignIdFor(\App\Models\Project::class)->constrained()->restrictOnDelete();
            $table->date('date')->index();
            $table->integer('total_seconds')->default(0);

            $table->softDeletes();
            $table->timestamps();
        });

        Schema::table('clock_entries', function (Blueprint $table) {
            $table->dropConstrainedForeignIdFor(\App\Models\User::class, 'user_id');
            $table->dropConstrainedForeignIdFor(\App\Models\Project::class, 'project_id');

            $table->foreignId('daily_log_id')->after('id')->nullable()->constrained('daily_logs')->onDelete('cascade');
        });

        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropConstrainedForeignIdFor(\App\Models\ClockEntry::class, 'clock_entry_id');

            $table->foreignId('daily_log_id')->after('id')->constrained('daily_logs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropConstrainedForeignIdFor(\App\Models\DailyLog::class, 'daily_log_id');
            $table->foreignIdFor(\App\Models\ClockEntry::class)->constrained()->onDelete('cascade');
        });

        Schema::table('clock_entries', function (Blueprint $table) {
            $table->dropConstrainedForeignIdFor(\App\Models\DailyLog::class, 'daily_log_id');
            $table->foreignIdFor(\App\Models\User::class)->constrained()->onDelete('cascade');
            $table->foreignIdFor(\App\Models\Project::class)->constrained()->restrictOnDelete();
        });

        Schema::dropIfExists('daily_logs');
        DB::statement('CREATE VIEW daily_logs_view AS
            SELECT
                "user_id",
                "project_id",
                DATE("in") AS "date",
                SUM("duration_seconds") AS "duration_seconds"
            FROM
                "clock_entries"
            GROUP BY
                "user_id",
                "date",
                "project_id"
            ;'
        );
    }
};
