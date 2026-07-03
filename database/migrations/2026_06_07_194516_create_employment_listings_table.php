<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employment_listings', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $table->text('description')->nullable();
            $table->text('requirements')->nullable();
            $table->string('employment_type', 50)->default('full-time'); // full-time, part-time, contract, internship
            $table->unsignedSmallInteger('vacancies')->default(1);
            $table->date('deadline')->nullable();
            $table->string('status', 20)->default('open'); // open, closed, draft
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employment_listings');
    }
};
