<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('continuous_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_offering_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->string('ca_name');
            $table->enum('ca_type', ['assignment', 'quiz', 'test', 'project', 'presentation', 'other'])->default('assignment');
            $table->decimal('score', 5, 2)->default(0);
            $table->decimal('max_score', 5, 2)->default(100);
            $table->text('remarks')->nullable();
            $table->date('assessment_date')->nullable();
            $table->timestamps();
        });

        Schema::create('final_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_offering_id')->constrained()->onDelete('cascade');
            $table->decimal('ca_total', 5, 2)->default(0);
            $table->decimal('exam_score', 5, 2)->default(0);
            $table->decimal('total_score', 5, 2)->default(0);
            $table->string('grade', 5)->nullable();
            $table->decimal('grade_points', 3, 1)->default(0);
            $table->enum('status', ['pending', 'approved', 'published', 'withheld'])->default('pending');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->unique(['student_id', 'course_offering_id']);
        });

        Schema::create('gpa_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('semester_id')->constrained()->onDelete('cascade');
            $table->decimal('gpa', 4, 2)->default(0);
            $table->decimal('cgpa', 4, 2)->default(0);
            $table->integer('credit_hours_attempted')->default(0);
            $table->integer('credit_hours_earned')->default(0);
            $table->string('academic_standing', 50)->default('Good Standing');
            $table->timestamps();
            $table->unique(['student_id', 'semester_id']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('gpa_records');
        Schema::dropIfExists('final_results');
        Schema::dropIfExists('continuous_assessments');
    }
};
