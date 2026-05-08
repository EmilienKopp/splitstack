<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Splitstack\Translucid\Facades\Translucid;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Translucid::observe(User::class);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Translucid::unobserve(User::class);
    }
};
