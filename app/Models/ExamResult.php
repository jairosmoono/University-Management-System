<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamResult extends Model {
    use HasFactory;

    protected $fillable = ['examination_id', 'course_offering_id', 'student_id', 'marks_obtained', 'grade_points', 'grade', 'is_absent', 'remarks', 'entered_by'];
    protected $casts = ['marks_obtained' => 'decimal:2', 'is_absent' => 'boolean'];

    public function examination() { return $this->belongsTo(Examination::class); }
    public function student() { return $this->belongsTo(Student::class); }
    public function enteredBy() { return $this->belongsTo(User::class, 'entered_by'); }
}
