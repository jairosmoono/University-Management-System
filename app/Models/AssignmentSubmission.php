<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignmentSubmission extends Model {
    use HasFactory;

    protected $fillable = ['assignment_id', 'student_id', 'submission_text', 'file_path', 'submitted_at', 'marks_obtained', 'feedback', 'graded_by', 'graded_at', 'status'];
    protected $casts = ['marks_obtained' => 'decimal:2', 'submitted_at' => 'datetime', 'graded_at' => 'datetime'];

    public function assignment() { return $this->belongsTo(Assignment::class); }
    public function student() { return $this->belongsTo(Student::class); }
    public function gradedBy() { return $this->belongsTo(\App\Models\User::class, 'graded_by'); }

    public function getPercentageAttribute() {
        $max = $this->assignment?->total_marks ?? 100;
        return $max > 0 && $this->marks_obtained !== null ? round(($this->marks_obtained / $max) * 100, 1) : null;
    }
}
