<?php

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
            $table->foreignIdFor(User::class);
            $table->unique(['commit_hash', 'repository_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('git_logs', function (Blueprint $table) {
            $table->dropUnique(['commit_hash', 'repository_id', 'user_id']);
            $table->dropForeignIdFor(User::class);
        });
    }
};
