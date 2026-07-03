<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model {
    use HasFactory, SoftDeletes;

    protected $fillable = ['faculty_id', 'name', 'code', 'hod_id', 'description', 'email', 'phone', 'status'];

    public function faculty() { return $this->belongsTo(Faculty::class); }
    public function hod() { return $this->belongsTo(Staff::class, 'hod_id'); }
    public function programs() { return $this->hasMany(Program::class); }
    public function courses() { return $this->hasMany(Course::class); }
    public function staff() { return $this->hasMany(Staff::class); }
    public function employees() { return $this->hasMany(Employee::class); }

    // Students reach department through programs (students.program_id → programs.department_id)
    public function students() {
        return $this->hasManyThrough(Student::class, Program::class, 'department_id', 'program_id');
    }

    public function budgets() { return $this->hasMany(DepartmentBudget::class); }
    public function scopeActive($query) { return $query->where('status', 'active'); }
}
