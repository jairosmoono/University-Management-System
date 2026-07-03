<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->string('category', 100)->nullable()->after('type');
            $table->string('file_name', 255)->nullable()->after('file_path');
            $table->string('file_type', 100)->nullable()->after('file_name');
            $table->boolean('is_public')->default(false)->after('file_size');
            $table->string('status', 20)->default('active')->after('is_public');
            $table->unsignedInteger('download_count')->default(0)->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn(['category', 'file_name', 'file_type', 'is_public', 'status', 'download_count']);
        });
    }
};
