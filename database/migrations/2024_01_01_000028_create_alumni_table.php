<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('alumni', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->year('graduation_year');
            $table->string('degree_obtained')->nullable();
            $table->enum('employment_status', ['employed', 'self_employed', 'unemployed', 'student', 'other'])->default('unemployed');
            $table->string('employer')->nullable();
            $table->string('job_title')->nullable();
            $table->string('industry')->nullable();
            $table->string('current_email')->nullable();
            $table->string('current_phone', 20)->nullable();
            $table->text('current_address')->nullable();
            $table->string('linkedin_url')->nullable();
            $table->boolean('willing_to_mentor')->default(false);
            $table->text('achievements')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('alumni'); }
};
