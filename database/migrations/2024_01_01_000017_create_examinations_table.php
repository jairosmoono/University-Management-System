<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('examinations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_offering_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->enum('type', ['mid_term', 'final', 'supplementary', 'resit'])->default('final');
            $table->date('exam_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('venue')->nullable();
            $table->decimal('total_marks', 5, 2)->default(100);
            $table->decimal('pass_mark', 5, 2)->default(40);
            $table->text('instructions')->nullable();
            $table->unsignedBigInteger('invigilator_id')->nullable();
            $table->enum('status', ['scheduled', 'ongoing', 'completed', 'cancelled'])->default('scheduled');
            $table->timestamps();
        });

        Schema::create('exam_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('examination_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->decimal('marks_obtained', 5, 2)->nullable();
            $table->string('grade', 5)->nullable();
            $table->boolean('is_absent')->default(false);
            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('entered_by')->nullable();
            $table->timestamps();
            $table->unique(['examination_id', 'student_id']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('exam_results');
        Schema::dropIfExists('examinations');
    }
};
