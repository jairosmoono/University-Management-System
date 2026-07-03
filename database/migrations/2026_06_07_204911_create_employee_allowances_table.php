<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_allowances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->string('allowance_type', 60); // housing, transport, medical, meal, entertainment, other
            $table->string('description', 255)->nullable();
            $table->decimal('amount', 12, 2)->default(0);      // fixed amount
            $table->decimal('percentage', 5, 2)->default(0);   // % of basic salary (0 = use fixed amount)
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_allowances');
    }
};
