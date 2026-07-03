<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('research_projects', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('reference_number', 30)->unique()->nullable();
            $table->unsignedBigInteger('lead_researcher_id')->nullable();
            $table->foreignId('department_id')->nullable()->constrained()->onDelete('set null');
            $table->text('description')->nullable();
            $table->text('objectives')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->decimal('budget', 14, 2)->nullable();
            $table->string('funding_source')->nullable();
            $table->enum('status', ['proposed', 'approved', 'ongoing', 'completed', 'suspended'])->default('proposed');
            $table->timestamps();
        });

        Schema::create('publications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('research_project_id')->nullable()->constrained()->onDelete('set null');
            $table->string('title');
            $table->text('authors');
            $table->string('journal_name')->nullable();
            $table->string('volume')->nullable();
            $table->string('issue')->nullable();
            $table->string('pages')->nullable();
            $table->year('year');
            $table->string('doi')->nullable();
            $table->string('isbn')->nullable();
            $table->enum('type', ['journal_article', 'conference_paper', 'book', 'book_chapter', 'thesis', 'technical_report'])->default('journal_article');
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('publications');
        Schema::dropIfExists('research_projects');
    }
};
