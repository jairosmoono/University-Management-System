<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('course_offerings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('semester_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('lecturer_id')->nullable();
            $table->string('room', 50)->nullable();
            $table->integer('max_students')->default(50);
            $table->integer('enrolled_students')->default(0);
            $table->enum('status', ['active', 'cancelled', 'completed'])->default('active');
            $table->timestamps();
            $table->unique(['course_id', 'semester_id']);
            $table->foreign('lecturer_id')->references('id')->on('staff')->onDelete('set null');
        });
    }
    public function down(): void { Schema::dropIfExists('course_offerings'); }
};
