<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_deductions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->string('deduction_type', 60); // loan_repayment, union_dues, other
            $table->string('description', 255)->nullable();
            $table->decimal('amount', 12, 2);
            $table->boolean('is_recurring')->default(true); // deducted every payroll run
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_deductions');
    }
};
