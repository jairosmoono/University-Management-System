<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model {
    use HasFactory, SoftDeletes;

    // Actual DB columns: department_id, code, name, credits, level (varchar), course_type, description, prerequisites, status
    protected $fillable = ['department_id', 'name', 'code', 'credits', 'level', 'course_type', 'description', 'prerequisites', 'status'];

    public function department() { return $this->belongsTo(Department::class); }
    public function programs() { return $this->belongsToMany(Program::class, 'course_program')->withPivot('year_of_study', 'is_mandatory'); }
    public function offerings() { return $this->hasMany(CourseOffering::class); }
    public function currentOffering() { return $this->hasOne(CourseOffering::class)->whereHas('semester', fn($q) => $q->where('is_current', true)); }
    public function prerequisites() { return $this->hasMany(CoursePrerequisite::class); }
    public function dependentCourses() { return $this->hasMany(CoursePrerequisite::class, 'prerequisite_course_id'); }
    public function scopeActive($query) { return $query->where('courses.status', 'active'); }
}
