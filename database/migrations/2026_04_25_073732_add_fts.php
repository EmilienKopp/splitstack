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
        $conn = DB::connection('tenant');
        $conn->statement('CREATE EXTENSION IF NOT EXISTS vector;');
        $conn->statement('CREATE EXTENSION IF NOT EXISTS pg_trgm;');
        $conn->statement('CREATE EXTENSION IF NOT EXISTS pg_textsearch;');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $conn = DB::connection('tenant');
        $conn->statement('DROP EXTENSION IF EXISTS vector;');
        $conn->statement('DROP EXTENSION IF EXISTS pg_trgm;');
        $conn->statement('DROP EXTENSION IF EXISTS pg_textsearch;');
    }
};
