<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grade_scales', function (Blueprint $table) {
            $table->id();
            $table->string('grade', 5)->unique();
            $table->decimal('min_score', 5, 2);
            $table->decimal('grade_points', 4, 2)->default(0);
            $table->string('label', 60)->nullable();
            $table->boolean('is_pass')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('exam_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('code', 50)->unique();
            $table->enum('category', ['ca', 'exam', 'other'])->default('other');
            $table->string('description', 255)->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        // Seed default grade scales
        DB::table('grade_scales')->insert([
            ['grade'=>'A+','min_score'=>90,'grade_points'=>4.0,'label'=>'Distinction','is_pass'=>1,'sort_order'=>1,'created_at'=>now(),'updated_at'=>now()],
            ['grade'=>'A', 'min_score'=>85,'grade_points'=>4.0,'label'=>'Excellent',  'is_pass'=>1,'sort_order'=>2,'created_at'=>now(),'updated_at'=>now()],
            ['grade'=>'A-','min_score'=>80,'grade_points'=>3.7,'label'=>'Very Good',  'is_pass'=>1,'sort_order'=>3,'created_at'=>now(),'updated_at'=>now()],
            ['grade'=>'B+','min_score'=>75,'grade_points'=>3.3,'label'=>'Good',       'is_pass'=>1,'sort_order'=>4,'created_at'=>now(),'updated_at'=>now()],
            ['grade'=>'B', 'min_score'=>70,'grade_points'=>3.0,'label'=>'Good',       'is_pass'=>1,'sort_order'=>5,'created_at'=>now(),'updated_at'=>now()],
            ['grade'=>'B-','min_score'=>65,'grade_points'=>2.7,'label'=>'Above Average','is_pass'=>1,'sort_order'=>6,'created_at'=>now(),'updated_at'=>now()],
            ['grade'=>'C+','min_score'=>60,'grade_points'=>2.3,'label'=>'Average',    'is_pass'=>1,'sort_order'=>7,'created_at'=>now(),'updated_at'=>now()],
            ['grade'=>'C', 'min_score'=>55,'grade_points'=>2.0,'label'=>'Average',    'is_pass'=>1,'sort_order'=>8,'created_at'=>now(),'updated_at'=>now()],
            ['grade'=>'C-','min_score'=>50,'grade_points'=>1.7,'label'=>'Pass',       'is_pass'=>1,'sort_order'=>9,'created_at'=>now(),'updated_at'=>now()],
            ['grade'=>'D+','min_score'=>45,'grade_points'=>1.3,'label'=>'Pass',       'is_pass'=>1,'sort_order'=>10,'created_at'=>now(),'updated_at'=>now()],
            ['grade'=>'D', 'min_score'=>40,'grade_points'=>1.0,'label'=>'Pass',       'is_pass'=>1,'sort_order'=>11,'created_at'=>now(),'updated_at'=>now()],
            ['grade'=>'D-','min_score'=>35,'grade_points'=>0.7,'label'=>'Marginal',   'is_pass'=>0,'sort_order'=>12,'created_at'=>now(),'updated_at'=>now()],
            ['grade'=>'F', 'min_score'=>0, 'grade_points'=>0.0,'label'=>'Fail',       'is_pass'=>0,'sort_order'=>13,'created_at'=>now(),'updated_at'=>now()],
        ]);

        // Seed default exam types
        DB::table('exam_types')->insert([
            ['name'=>'Supplementary Assessment','code'=>'supplementary','category'=>'ca',   'description'=>'Continuous assessment test','is_active'=>1,'sort_order'=>1,'created_at'=>now(),'updated_at'=>now()],
            ['name'=>'Midterm Examination',      'code'=>'mid_term',    'category'=>'ca',   'description'=>'Mid-semester examination',  'is_active'=>1,'sort_order'=>2,'created_at'=>now(),'updated_at'=>now()],
            ['name'=>'Final Examination',        'code'=>'final',       'category'=>'exam', 'description'=>'End of semester examination','is_active'=>1,'sort_order'=>3,'created_at'=>now(),'updated_at'=>now()],
            ['name'=>'Quiz',                     'code'=>'quiz',        'category'=>'ca',   'description'=>'Short in-class quiz',       'is_active'=>1,'sort_order'=>4,'created_at'=>now(),'updated_at'=>now()],
            ['name'=>'Assignment',               'code'=>'assignment',  'category'=>'ca',   'description'=>'Take-home assignment',      'is_active'=>1,'sort_order'=>5,'created_at'=>now(),'updated_at'=>now()],
            ['name'=>'Practical',                'code'=>'practical',   'category'=>'other','description'=>'Lab or practical session',  'is_active'=>1,'sort_order'=>6,'created_at'=>now(),'updated_at'=>now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_types');
        Schema::dropIfExists('grade_scales');
    }
};
