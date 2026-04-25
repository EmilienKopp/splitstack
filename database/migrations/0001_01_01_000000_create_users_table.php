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
        // Introspect the landlord database connection to determine the tenant_users.user_id column type
        // Default to unsignedBigInteger if we can't determine the type
        $userIdColumnType = 'unsignedBigInteger';
        try {
            $usersTableUserIdType = Schema::connection('landlord')->getColumnType('users', 'id');
            $userIdColumnType = match ($usersTableUserIdType) {
                'integer' => 'unsignedInteger',
                'bigint' => 'unsignedBigInteger',
                'uuid', 'guid' => 'uuid',
                default => $userIdColumnType,
            };
        } catch (Exception $e) {
            // If there's an error (e.g. the users table doesn't exist yet), we'll just use the default type
        }

        Schema::create('users', function (Blueprint $table) use ($userIdColumnType) {
            $table->$userIdColumnType('id')->primary();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('workos_id')->unique();
            $table->rememberToken();
            $table->text('avatar');
            $table->timestamps();
        });

        Schema::create('sessions', function (Blueprint $table) use ($userIdColumnType) {
            $table->string('id')->primary();
            $table->$userIdColumnType('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('sessions');
    }
};
