<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinalResult extends Model {
    use HasFactory;

    // Actual DB columns: student_id, course_offering_id, academic_year_id, semester_id,
    // ca_score, exam_score, total_score, grade, grade_points, status
    protected $fillable = [
        'student_id', 'course_offering_id', 'academic_year_id', 'semester_id',
        'ca_score', 'exam_score', 'total_score', 'grade', 'grade_points', 'status',
    ];
    protected $casts = [
        'ca_score'     => 'decimal:2',
        'exam_score'   => 'decimal:2',
        'total_score'  => 'decimal:2',
        'grade_points' => 'decimal:1',
    ];

    public function student() { return $this->belongsTo(Student::class); }
    public function courseOffering() { return $this->belongsTo(CourseOffering::class); }
    public function approvedBy() { return $this->belongsTo(User::class, 'approved_by'); }
    public function gradeAppeals() { return $this->hasMany(GradeAppeal::class); }

    public function getPassFailAttribute() { return $this->total_score >= 40 ? 'PASS' : 'FAIL'; }
}
