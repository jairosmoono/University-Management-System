<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ELearningQuiz extends Model {
    use HasFactory;

    protected $table    = 'elearning_quizzes';
    protected $fillable = [
        'elearning_course_id', 'title', 'description',
        'time_limit_minutes', 'passing_score', 'max_attempts', 'is_published',
    ];
    protected $casts = ['is_published' => 'boolean'];

    public function course()    { return $this->belongsTo(ELearningCourse::class, 'elearning_course_id', 'id'); }
    public function questions() { return $this->hasMany(ELearningQuestion::class, 'quiz_id')->orderBy('sort_order'); }
    public function attempts()  { return $this->hasMany(ELearningQuizAttempt::class, 'quiz_id'); }

    public function getTotalMarksAttribute(): int {
        return $this->questions()->sum('marks');
    }

    public function getAttemptsForStudent(int $studentId) {
        return $this->attempts()->where('student_id', $studentId)->orderByDesc('attempt_number')->get();
    }

    public function canStudentAttempt(int $studentId): bool {
        $count = $this->attempts()->where('student_id', $studentId)->count();
        return $count < $this->max_attempts;
    }
}
