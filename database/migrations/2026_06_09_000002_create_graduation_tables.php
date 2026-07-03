<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('graduation_ceremonies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->date('ceremony_date');
            $table->string('venue')->nullable();
            $table->string('dress_code')->nullable();
            $table->unsignedInteger('max_graduates')->nullable();
            $table->enum('status', ['planned', 'confirmed', 'completed', 'cancelled'])->default('planned');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('graduation_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('program_id')->constrained()->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->foreignId('ceremony_id')->nullable()->constrained('graduation_ceremonies')->nullOnDelete();
            $table->decimal('cgpa', 4, 2)->default(0);
            $table->unsignedSmallInteger('credits_earned')->default(0);
            $table->enum('status', ['pending', 'under_review', 'cleared', 'approved', 'rejected', 'graduated'])->default('pending');
            $table->boolean('finance_cleared')->default(false);
            $table->boolean('library_cleared')->default(false);
            $table->boolean('academic_cleared')->default(false);
            $table->timestamp('cleared_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->date('graduation_date')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique('student_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('graduation_applications');
        Schema::dropIfExists('graduation_ceremonies');
    }
};
