<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Semester extends Model {
    use HasFactory;

    protected $fillable = ['academic_year_id', 'name', 'start_date', 'end_date', 'registration_start', 'registration_end', 'exam_start', 'exam_end', 'is_current', 'status'];
    protected $casts = ['start_date' => 'date', 'end_date' => 'date', 'registration_start' => 'date', 'registration_end' => 'date', 'exam_start' => 'date', 'exam_end' => 'date', 'is_current' => 'boolean'];

    public function academicYear() { return $this->belongsTo(AcademicYear::class); }
    public function courseOfferings() { return $this->hasMany(CourseOffering::class); }
    public function timetables() { return $this->hasManyThrough(Timetable::class, CourseOffering::class); }
    public function studentBills() { return $this->hasMany(StudentBill::class); }
    public function gpaRecords() { return $this->hasMany(GpaRecord::class); }

    public static function current() { return static::where('is_current', true)->first(); }
    public function scopeCurrent($query) { return $query->where('is_current', true); }

    public function isRegistrationOpen() {
        $today = now()->toDateString();
        return $this->registration_start && $this->registration_end && $today >= $this->registration_start->toDateString() && $today <= $this->registration_end->toDateString();
    }
}
