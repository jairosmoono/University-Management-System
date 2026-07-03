<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('research_papers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('research_project_id')->nullable()->constrained('research_projects')->nullOnDelete();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->text('authors')->nullable();
            $table->text('abstract')->nullable();
            $table->string('keywords')->nullable();
            $table->string('file_path');
            $table->string('file_original_name');
            $table->unsignedBigInteger('file_size')->nullable();
            $table->string('file_mime', 100)->nullable();
            $table->enum('category', [
                'journal_article', 'conference_paper', 'thesis',
                'technical_report', 'book_chapter', 'other',
            ])->default('other');
            $table->string('publication_year', 4)->nullable();
            $table->string('doi')->nullable();
            $table->boolean('is_public')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('research_papers');
    }
};
