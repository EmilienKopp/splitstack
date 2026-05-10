<?php

use App\Enums\BudgetStatus;
use App\Enums\ExpenseStatus;
use App\Enums\RateFrequency;
use App\Enums\RateTypeScope;
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
        Schema::create('rate_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('scope', RateTypeScope::values())->default(RateTypeScope::Organization);
            $table->foreignId('organization_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('project_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rate_type_id')->constrained()->onDelete('cascade');
            $table->enum('rate_frequency', RateFrequency::values())->default(RateFrequency::Hourly);
            $table->foreignId('organization_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('project_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('JPY');
            $table->decimal('overtime_multiplier', 4, 2)->default(1.25);
            $table->decimal('holiday_multiplier', 4, 2)->default(1.35);
            $table->decimal('special_multiplier', 4, 2)->default(1.5);
            $table->decimal('custom_multiplier_rate', 4, 2)->nullable();
            $table->text('custom_multiplier_label')->nullable();
            $table->boolean('is_default')->default(false);
            $table->dateTime('effective_from')->nullable();
            $table->dateTime('effective_until')->nullable();
            $table->timestamps();

            // Ensure only one default rate per scope
            $table->unique(['organization_id', 'project_id', 'user_id', 'is_default'], 'unique_default_rate');
        });

        Schema::create('budgets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 12, 2);
            $table->decimal('amount_low', 12, 2)->nullable();
            $table->decimal('amount_high', 12, 2)->nullable();
            $table->string('currency', 3)->default('JPY');
            $table->enum('type', RateFrequency::values())->default(RateFrequency::Fixed);
            $table->enum('status', BudgetStatus::values())->default(BudgetStatus::Draft);
            $table->integer('allocated_hours')->nullable();
            $table->dateTime('start_date');
            $table->dateTime('end_date')->nullable();
            $table->timestamps();
        });

        Schema::create('budget_adjustments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('budget_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('adjustment_amount', 12, 2);
            $table->decimal('adjustment_amount_low', 12, 2)->nullable();
            $table->decimal('adjustment_amount_high', 12, 2)->nullable();
            $table->string('currency', 3)->default('JPY');
            $table->text('reason')->nullable();
            $table->timestamps();
        });

        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('budget_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('JPY');
            $table->string('description')->nullable();
            $table->enum('status', ExpenseStatus::values())->default(ExpenseStatus::Draft);
            $table->date('expense_date');
            $table->timestamps();
        });

        Schema::table('activity_logs', function (Blueprint $table) {
            $table->foreignId('expense_id')->nullable()->constrained()->nullOnDelete();
        });

        Schema::table('clock_entries', function (Blueprint $table) {
            $table->foreignId('rate_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('applied_rate', 10, 2)->nullable();
            $table->string('currency', 3)->default('JPY');
            $table->decimal('amount', 12, 2)->nullable()->storedAs('CASE 
                WHEN "applied_rate" IS NOT NULL AND "in" IS NOT NULL AND "out" IS NOT NULL 
                THEN ( EXTRACT(EPOCH FROM ("out" - "in")) / 3600.0) * applied_rate 
                ELSE NULL 
            END');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropColumn('expense_id');
        });

        Schema::table('clock_entries', function (Blueprint $table) {
            $table->dropColumn(['rate_id', 'amount', 'applied_rate', 'currency']);
        });

        Schema::dropIfExists('expenses');
        Schema::dropIfExists('budget_adjustments');
        Schema::dropIfExists('budgets');
        Schema::dropIfExists('rates');
        Schema::dropIfExists('rate_types');
    }
};
