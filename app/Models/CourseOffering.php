<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseOffering extends Model {
    use HasFactory;

    // Actual DB columns: course_id, academic_year_id, semester_id, lecturer_id, venue, schedule, max_students, enrolled_students, status
    protected $fillable = ['course_id', 'academic_year_id', 'semester_id', 'lecturer_id', 'venue', 'schedule', 'max_students', 'enrolled_students', 'status'];

    public function course() { return $this->belongsTo(Course::class); }
    public function academicYear() { return $this->belongsTo(AcademicYear::class); }
    public function semester() { return $this->belongsTo(Semester::class); }
    public function lecturer() { return $this->belongsTo(Staff::class, 'lecturer_id'); }
    public function registrations() { return $this->hasMany(CourseRegistration::class); }
    public function approvedRegistrations() { return $this->hasMany(CourseRegistration::class)->where('status', 'registered'); }
    public function timetables() { return $this->hasMany(Timetable::class); }
    public function attendanceSessions() { return $this->hasMany(AttendanceSession::class); }
    public function examinations() { return $this->hasMany(Examination::class); }
    public function finalResults() { return $this->hasMany(FinalResult::class); }
    public function continuousAssessments() { return $this->hasMany(ContinuousAssessment::class); }
    public function assignments() { return $this->hasMany(Assignment::class); }
    public function elearningCourse() { return $this->hasOne(ELearningCourse::class); }
    public function waitlist() { return $this->hasMany(CourseWaitlist::class)->orderBy('position'); }

    public function getAvailableSlotsAttribute() { return max(0, $this->max_students - $this->enrolled_students); }
    public function getIsFullAttribute() { return $this->enrolled_students >= $this->max_students; }
}
