<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('admissions', function (Blueprint $table) {
            $table->id();
            $table->string('application_number', 30)->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('middle_name')->nullable();
            $table->string('email');
            $table->string('phone', 20)->nullable();
            $table->date('dob')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->string('nationality', 60)->default('Zambian');
            $table->foreignId('program_id')->constrained()->onDelete('restrict');
            $table->foreignId('academic_year_id')->constrained()->onDelete('restrict');
            $table->text('personal_statement')->nullable();
            $table->text('qualifications')->nullable();
            $table->string('previous_institution')->nullable();
            $table->enum('status', ['pending', 'under_review', 'interview_scheduled', 'approved', 'rejected', 'enrolled'])->default('pending');
            $table->timestamp('applied_at')->useCurrent();
            $table->timestamp('reviewed_at')->nullable();
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->text('review_notes')->nullable();
            $table->date('interview_date')->nullable();
            $table->string('interview_venue')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('admissions'); }
};
