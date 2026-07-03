<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ELearningLessonCompletion extends Model {
    use HasFactory;

    protected $table    = 'elearning_lesson_completions';
    protected $fillable = ['student_id', 'lesson_id', 'completed_at'];
    protected $casts    = ['completed_at' => 'datetime'];

    public function student() { return $this->belongsTo(Student::class); }
    public function lesson()  { return $this->belongsTo(ELearningLesson::class, 'lesson_id'); }
}
