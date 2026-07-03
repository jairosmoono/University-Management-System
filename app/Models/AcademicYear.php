<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicYear extends Model {
    use HasFactory;

    protected $fillable = ['name', 'start_date', 'end_date', 'is_current', 'status'];
    protected $casts = ['start_date' => 'date', 'end_date' => 'date', 'is_current' => 'boolean'];

    public function semesters() { return $this->hasMany(Semester::class); }
    public function currentSemester() { return $this->hasOne(Semester::class)->where('is_current', true); }
    public function students() { return $this->hasMany(Student::class); }
    public function feeStructures() { return $this->hasMany(FeeStructure::class); }
    public function studentBills() { return $this->hasMany(StudentBill::class); }
    public function scholarships() { return $this->hasMany(Scholarship::class); }
    public function admissions() { return $this->hasMany(Admission::class); }
    public function roomAllocations() { return $this->hasMany(RoomAllocation::class); }

    public static function current() { return static::where('is_current', true)->first(); }
    public function scopeCurrent($query) { return $query->where('is_current', true); }
}
