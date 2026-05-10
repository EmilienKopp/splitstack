<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('DROP VIEW IF EXISTS daily_logs_view;');
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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP VIEW daily_logs_view;');
    }
};
