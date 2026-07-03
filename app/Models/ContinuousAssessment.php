<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContinuousAssessment extends Model {
    use HasFactory;

    protected $fillable = ['course_offering_id', 'student_id', 'ca_name', 'ca_type', 'score', 'max_score', 'remarks', 'assessment_date'];
    protected $casts = ['score' => 'decimal:2', 'max_score' => 'decimal:2', 'assessment_date' => 'date'];

    public function courseOffering() { return $this->belongsTo(CourseOffering::class); }
    public function student() { return $this->belongsTo(Student::class); }

    public function getPercentageAttribute() {
        return $this->max_score > 0 ? round(($this->score / $this->max_score) * 100, 2) : 0;
    }
}
