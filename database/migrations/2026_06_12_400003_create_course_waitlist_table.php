<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('course_waitlist', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_offering_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('position')->default(1);
            $table->enum('status', ['waiting', 'notified', 'enrolled', 'cancelled'])->default('waiting');
            $table->timestamp('notified_at')->nullable();
            $table->timestamps();
            $table->unique(['student_id', 'course_offering_id']);
        });
    }

    public function down(): void { Schema::dropIfExists('course_waitlist'); }
};
