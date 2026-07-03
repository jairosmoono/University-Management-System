<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('exam_results', function (Blueprint $table) {
            $table->renameColumn('exam_score', 'marks_obtained');
            $table->decimal('grade_points', 4, 2)->nullable()->after('marks_obtained');
            $table->string('grade', 5)->nullable()->after('grade_points');
            $table->boolean('is_absent')->default(false)->after('grade');
            $table->text('remarks')->nullable()->after('is_absent');
        });
    }

    public function down(): void
    {
        Schema::table('exam_results', function (Blueprint $table) {
            $table->dropColumn(['grade_points', 'grade', 'is_absent', 'remarks']);
            $table->renameColumn('marks_obtained', 'exam_score');
        });
    }
};
