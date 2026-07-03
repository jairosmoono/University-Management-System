<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payroll_item_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);            // display name, e.g. "Housing Allowance"
            $table->string('slug', 60)->unique();   // value used in forms, e.g. "housing"
            $table->string('category', 20);         // 'allowance' | 'deduction'
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        $now = now();
        DB::table('payroll_item_types')->insert([
            // Allowance types
            ['name' => 'Housing Allowance',       'slug' => 'housing',       'category' => 'allowance', 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Transport Allowance',     'slug' => 'transport',     'category' => 'allowance', 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Medical Allowance',       'slug' => 'medical',       'category' => 'allowance', 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Meal Allowance',          'slug' => 'meal',          'category' => 'allowance', 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Entertainment Allowance', 'slug' => 'entertainment', 'category' => 'allowance', 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Other Allowance',         'slug' => 'other_allowance','category' => 'allowance', 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            // Deduction types
            ['name' => 'Loan Repayment',          'slug' => 'loan_repayment','category' => 'deduction', 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Union Dues',              'slug' => 'union_dues',    'category' => 'deduction', 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Other Deduction',         'slug' => 'other_deduction','category' => 'deduction', 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_item_types');
    }
};
