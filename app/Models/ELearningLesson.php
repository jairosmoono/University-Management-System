<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ELearningLesson extends Model {
    use HasFactory;

    protected $table    = 'elearning_lessons';
    protected $fillable = ['elearning_course_id', 'title', 'description', 'sort_order', 'is_published'];
    protected $casts    = ['is_published' => 'boolean'];

    public function course()      { return $this->belongsTo(ELearningCourse::class, 'elearning_course_id', 'id'); }
    public function items()       { return $this->hasMany(ELearningLessonItem::class, 'lesson_id')->orderBy('sort_order'); }
    public function completions() { return $this->hasMany(ELearningLessonCompletion::class, 'lesson_id'); }

    public function isCompletedByStudent(int $studentId): bool {
        return $this->completions()->where('student_id', $studentId)->exists();
    }
}
