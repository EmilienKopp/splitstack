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
        Schema::table('users', function (Blueprint $table) {
            $table->text('handle')->unique()->after('email');
            $table->text('first_name')->after('handle');
            $table->text('middle_name')->after('first_name')->nullable();
            $table->text('last_name')->after('first_name');
            $table->text('title')->after('last_name')->nullable();
            $table->text('phone_main')->after('title')->nullable();
            $table->text('phone_secondary')->after('phone_main')->nullable();
            $table->text('bio')->after('phone')->nullable();
            $table->jsonb('role_data')->after('avatar')->nullable();
            $table->text('facebook')->after('role_data')->nullable();
            $table->text('x_twitter')->after('facebook')->nullable();
            $table->text('instagram')->after('twitter')->nullable();
            $table->text('linkedin')->after('instagram')->nullable();
            $table->text('youtube')->after('linkedin')->nullable();
            $table->text('website')->after('youtube')->nullable();
            $table->text('github')->after('github')->nullable();
            $table->jsonb('dashboard_preferences')->after('github')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('handle');
            $table->dropColumn('first_name');
            $table->dropColumn('middle_name');
            $table->dropColumn('last_name');
            $table->dropColumn('title');
            $table->dropColumn('phone_main');
            $table->dropColumn('phone_secondary');
            $table->dropColumn('bio');
            $table->dropColumn('avatar');
            $table->dropColumn('role_data');
            $table->dropColumn('facebook');
            $table->dropColumn('x_twitter');
            $table->dropColumn('instagram');
            $table->dropColumn('linkedin');
            $table->dropColumn('youtube');
            $table->dropColumn('website');
            $table->dropColumn('github');
            $table->dropColumn('dashboard_preferences');
        });
    }
};
