<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('department_budgets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->constrained()->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->string('fiscal_year', 20); // e.g. "2025/2026"
            $table->decimal('total_budget', 15, 2)->default(0);
            $table->text('description')->nullable();
            $table->enum('status', ['draft', 'approved', 'active', 'closed'])->default('draft');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->unique(['department_id', 'academic_year_id']);
        });

        Schema::create('budget_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_budget_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['allocation', 'expense', 'transfer', 'adjustment']);
            $table->string('category', 80); // e.g. "Staff Salaries", "Equipment"
            $table->decimal('amount', 15, 2);
            $table->text('description')->nullable();
            $table->string('reference_no', 60)->nullable();
            $table->date('transaction_date');
            $table->foreignId('recorded_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('budget_transactions');
        Schema::dropIfExists('department_budgets');
    }
};
