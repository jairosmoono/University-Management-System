<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Nullify blank strings so multiple students without an NRC don't collide
        \DB::statement("UPDATE students SET national_id = NULL WHERE TRIM(national_id) = ''");

        Schema::table('students', function (Blueprint $table) {
            $table->string('national_id')->nullable()->unique()->change();
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropUnique(['national_id']);
        });
    }
};
