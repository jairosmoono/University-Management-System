<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GpaRecord extends Model {
    use HasFactory;

    protected $fillable = ['student_id', 'academic_year_id', 'semester_id', 'gpa', 'cgpa', 'credits_earned', 'total_credits_earned', 'academic_standing'];
    protected $casts = ['gpa' => 'decimal:2', 'cgpa' => 'decimal:2'];

    public function student() { return $this->belongsTo(Student::class); }
    public function semester() { return $this->belongsTo(Semester::class); }
}
