<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseRegistration extends Model {
    use HasFactory;

    protected $fillable = ['student_id', 'course_offering_id', 'registered_by', 'status'];
    protected $casts = [];

    public function student() { return $this->belongsTo(Student::class); }
    public function courseOffering() { return $this->belongsTo(CourseOffering::class); }
    public function approvedBy() { return $this->belongsTo(User::class, 'approved_by'); }
}
