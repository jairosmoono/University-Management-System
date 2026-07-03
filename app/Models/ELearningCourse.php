<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ELearningCourse extends Model {
    use HasFactory;

    protected $table    = 'elearning_courses';
    protected $fillable = ['course_offering_id', 'description', 'is_published', 'created_by'];
    protected $casts    = ['is_published' => 'boolean'];

    public function courseOffering() { return $this->belongsTo(CourseOffering::class); }
    public function createdBy()      { return $this->belongsTo(User::class, 'created_by'); }
    public function lessons()        { return $this->hasMany(ELearningLesson::class, 'elearning_course_id')->orderBy('sort_order'); }
    public function quizzes()        { return $this->hasMany(ELearningQuiz::class, 'elearning_course_id'); }

    public function getPublishedLessonsCountAttribute(): int {
        return $this->lessons()->where('is_published', true)->count();
    }

    public function getProgressForStudent(int $studentId): int {
        $total = $this->lessons()->where('is_published', true)->count();
        if ($total === 0) return 0;
        $done = ELearningLessonCompletion::whereHas('lesson', fn($q) => $q->where('elearning_course_id', $this->id))
            ->where('student_id', $studentId)->count();
        return (int) round($done / $total * 100);
    }
}
