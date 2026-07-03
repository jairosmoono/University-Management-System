<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('student_id', 30)->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('middle_name')->nullable();
            $table->date('dob');
            $table->enum('gender', ['male', 'female', 'other']);
            $table->string('nationality', 60)->default('Zambian');
            $table->string('religion', 60)->nullable();
            $table->string('blood_type', 5)->nullable();
            $table->string('nrc_number', 20)->nullable();
            $table->string('phone', 20)->nullable();
            $table->text('address')->nullable();
            $table->foreignId('faculty_id')->constrained()->onDelete('restrict');
            $table->foreignId('department_id')->constrained()->onDelete('restrict');
            $table->foreignId('program_id')->constrained()->onDelete('restrict');
            $table->foreignId('academic_year_id')->constrained()->onDelete('restrict');
            $table->integer('current_level')->default(1);
            $table->string('photo')->nullable();
            $table->date('admission_date');
            $table->date('graduation_date')->nullable();
            $table->enum('status', ['active', 'inactive', 'suspended', 'graduated', 'withdrawn', 'deferred'])->default('active');
            $table->string('admission_type', 30)->default('regular');
            $table->text('disability_info')->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone', 20)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
    public function down(): void { Schema::dropIfExists('students'); }
};
