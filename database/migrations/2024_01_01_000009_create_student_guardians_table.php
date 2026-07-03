<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('student_guardians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->enum('relationship', ['father', 'mother', 'guardian', 'spouse', 'sibling', 'other']);
            $table->string('phone', 20);
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->string('occupation')->nullable();
            $table->string('nrc_number', 20)->nullable();
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('student_guardians'); }
};
