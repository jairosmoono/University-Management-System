<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('hostels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('gender', ['male', 'female', 'mixed'])->default('mixed');
            $table->integer('total_rooms')->default(0);
            $table->unsignedBigInteger('warden_id')->nullable();
            $table->text('description')->nullable();
            $table->string('location')->nullable();
            $table->decimal('monthly_fee', 10, 2)->default(0);
            $table->enum('status', ['active', 'inactive', 'under_maintenance'])->default('active');
            $table->timestamps();
        });

        Schema::create('hostel_rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hostel_id')->constrained()->onDelete('cascade');
            $table->string('room_number', 20);
            $table->integer('capacity')->default(2);
            $table->integer('occupied')->default(0);
            $table->enum('type', ['single', 'double', 'triple', 'dormitory'])->default('double');
            $table->decimal('monthly_fee', 10, 2)->nullable();
            $table->text('amenities')->nullable();
            $table->enum('status', ['available', 'full', 'maintenance', 'reserved'])->default('available');
            $table->timestamps();
            $table->unique(['hostel_id', 'room_number']);
        });

        Schema::create('room_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hostel_room_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('academic_year_id')->constrained()->onDelete('cascade');
            $table->date('check_in_date');
            $table->date('check_out_date')->nullable();
            $table->decimal('fee_charged', 10, 2)->nullable();
            $table->enum('status', ['active', 'checked_out', 'terminated'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('room_allocations');
        Schema::dropIfExists('hostel_rooms');
        Schema::dropIfExists('hostels');
    }
};
