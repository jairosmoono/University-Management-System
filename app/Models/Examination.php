<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Examination extends Model {
    use HasFactory;

    protected $fillable = ['course_offering_id', 'name', 'type', 'exam_date', 'start_time', 'end_time', 'venue', 'max_marks', 'passing_marks', 'invigilator_id', 'status'];
    protected $casts = ['exam_date' => 'date', 'max_marks' => 'decimal:2', 'passing_marks' => 'decimal:2'];

    public function courseOffering() { return $this->belongsTo(CourseOffering::class); }
    public function results() { return $this->hasMany(ExamResult::class); }
    public function invigilator() { return $this->belongsTo(Staff::class, 'invigilator_id'); }
}
