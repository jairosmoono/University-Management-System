<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Staff extends Model {
    use HasFactory, SoftDeletes;

    // Actual DB columns: user_id, staff_id, department_id, designation, specialization, qualifications, status
    protected $fillable = ['user_id', 'staff_id', 'department_id', 'designation', 'specialization', 'qualifications', 'status'];
    protected $appends = ['full_name'];

    public function user() { return $this->belongsTo(User::class); }
    public function department() { return $this->belongsTo(Department::class); }
    public function courseOfferings() { return $this->hasMany(CourseOffering::class, 'lecturer_id'); }
    public function attendanceSessions() { return $this->hasMany(AttendanceSession::class, 'taken_by'); }

    // Name comes from the linked user record
    public function getFullNameAttribute() { return optional($this->user)->name ?? ''; }
    public function getNameAttribute() { return optional($this->user)->name ?? ''; }

    public function getPhotoUrlAttribute() {
        $avatar = optional($this->user)->avatar;
        return $avatar ? asset('storage/' . $avatar) : asset('images/default-staff.png');
    }

    public function scopeActive($query) { return $query->where('staff.status', 'active'); }
    public function scopeAcademic($query) { return $query; } // No staff_type column; returns all staff
}
