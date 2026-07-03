<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Faculty extends Model {
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'code', 'dean_id', 'description', 'status'];

    public function departments() { return $this->hasMany(Department::class); }
    public function activeDepartments() { return $this->hasMany(Department::class)->where('status', 'active'); }

    // Programs and staff reach faculty through departments
    public function programs() { return $this->hasManyThrough(Program::class, Department::class, 'faculty_id', 'department_id'); }
    public function staff() { return $this->hasManyThrough(Staff::class, Department::class, 'faculty_id', 'department_id'); }

    public function dean() { return $this->belongsTo(Staff::class, 'dean_id'); }

    // Students reach faculty through programs→departments (3 levels, no native hasManyThrough)
    public function studentsQuery() {
        $programIds = Program::whereIn('department_id', $this->departments()->pluck('id'))->pluck('id');
        return Student::whereIn('program_id', $programIds);
    }

    public function getTotalStudentsAttribute() {
        return $this->studentsQuery()->where('status', 'active')->count();
    }

    public function getTotalStaffAttribute() { return $this->staff()->where('status', 'active')->count(); }
    public function scopeActive($query) { return $query->where('status', 'active'); }
}
