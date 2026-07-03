<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $table->string('position');
            $table->date('appointment_date');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->decimal('salary', 12, 2)->nullable();
            $table->string('contract_type', 50)->default('permanent'); // permanent, contract, probation, acting
            $table->text('notes')->nullable();
            $table->string('status', 20)->default('active'); // active, expired, terminated
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_appointments');
    }
};
