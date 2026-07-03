<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ELearningQuizAttempt extends Model {
    use HasFactory;

    protected $table    = 'elearning_quiz_attempts';
    protected $fillable = [
        'student_id', 'quiz_id', 'attempt_number',
        'score', 'passed', 'answers', 'started_at', 'submitted_at',
    ];
    protected $casts = [
        'passed'       => 'boolean',
        'answers'      => 'array',
        'started_at'   => 'datetime',
        'submitted_at' => 'datetime',
        'score'        => 'decimal:2',
    ];

    public function student() { return $this->belongsTo(Student::class); }
    public function quiz()    { return $this->belongsTo(ELearningQuiz::class, 'quiz_id'); }
}
