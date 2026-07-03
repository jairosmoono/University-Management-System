<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('elearning_courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_offering_id')->unique()->constrained()->cascadeOnDelete();
            $table->text('description')->nullable();
            $table->boolean('is_published')->default(false);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('elearning_lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('elearning_course_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });

        Schema::create('elearning_lesson_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')->constrained('elearning_lessons')->cascadeOnDelete();
            $table->string('title');
            $table->enum('content_type', ['video_url', 'pdf_upload', 'text_html', 'external_link'])->default('text_html');
            $table->text('content');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('elearning_lesson_completions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lesson_id')->constrained('elearning_lessons')->cascadeOnDelete();
            $table->timestamp('completed_at')->useCurrent();
            $table->timestamps();
            $table->unique(['student_id', 'lesson_id']);
        });

        Schema::create('elearning_quizzes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('elearning_course_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('time_limit_minutes')->nullable();
            $table->unsignedTinyInteger('passing_score')->default(50);
            $table->unsignedTinyInteger('max_attempts')->default(3);
            $table->boolean('is_published')->default(false);
            $table->timestamps();
        });

        Schema::create('elearning_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained('elearning_quizzes')->cascadeOnDelete();
            $table->text('question_text');
            $table->enum('question_type', ['single_choice', 'true_false'])->default('single_choice');
            $table->unsignedSmallInteger('marks')->default(1);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('elearning_question_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained('elearning_questions')->cascadeOnDelete();
            $table->string('option_text');
            $table->boolean('is_correct')->default(false);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('elearning_quiz_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('quiz_id')->constrained('elearning_quizzes')->cascadeOnDelete();
            $table->unsignedTinyInteger('attempt_number')->default(1);
            $table->decimal('score', 5, 2)->default(0);
            $table->boolean('passed')->default(false);
            $table->json('answers')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('elearning_quiz_attempts');
        Schema::dropIfExists('elearning_question_options');
        Schema::dropIfExists('elearning_questions');
        Schema::dropIfExists('elearning_quizzes');
        Schema::dropIfExists('elearning_lesson_completions');
        Schema::dropIfExists('elearning_lesson_items');
        Schema::dropIfExists('elearning_lessons');
        Schema::dropIfExists('elearning_courses');
    }
};
