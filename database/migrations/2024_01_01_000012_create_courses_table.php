<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('code', 20)->unique();
            $table->integer('credit_hours')->default(3);
            $table->integer('lecture_hours')->default(3);
            $table->integer('lab_hours')->default(0);
            $table->enum('level', ['100', '200', '300', '400', '500', '600', '700'])->default('100');
            $table->enum('semester_offered', ['first', 'second', 'both'])->default('both');
            $table->text('description')->nullable();
            $table->string('prerequisites')->nullable();
            $table->enum('type', ['compulsory', 'elective', 'optional'])->default('compulsory');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('course_program', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('program_id')->constrained()->onDelete('cascade');
            $table->integer('semester_number')->default(1);
            $table->unique(['course_id', 'program_id']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('course_program');
        Schema::dropIfExists('courses');
    }
};
