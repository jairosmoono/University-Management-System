<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model {
    use HasFactory, SoftDeletes;

    // Actual DB columns: user_id, student_id, program_id, enrollment_date, expected_graduation,
    // year_of_study, admission_type, gender, date_of_birth, nationality, national_id,
    // phone, address, sponsor, photo, status
    protected $fillable = [
        'user_id', 'student_id', 'program_id', 'enrollment_date', 'expected_graduation',
        'year_of_study', 'admission_type', 'gender', 'date_of_birth', 'nationality',
        'national_id', 'phone', 'address', 'sponsor', 'photo', 'status',
    ];
    protected $casts = [
        'enrollment_date'    => 'date',
        'expected_graduation'=> 'date',
        'date_of_birth'      => 'date',
    ];
    protected $appends = ['full_name', 'photo_url'];

    public function user() { return $this->belongsTo(User::class); }
    public function program() { return $this->belongsTo(Program::class); }
    public function guardians() { return $this->hasMany(StudentGuardian::class); }
    public function primaryGuardian() { return $this->hasOne(StudentGuardian::class)->where('is_emergency_contact', true)->orderBy('id'); }
    public function courseRegistrations() { return $this->hasMany(CourseRegistration::class); }
    public function attendanceRecords() { return $this->hasMany(AttendanceRecord::class); }
    public function finalResults() { return $this->hasMany(FinalResult::class); }
    public function gpaRecords() { return $this->hasMany(GpaRecord::class); }
    public function bills() { return $this->hasMany(StudentBill::class); }
    public function payments() { return $this->hasMany(Payment::class); }
    public function currentBill() { return $this->hasOne(StudentBill::class)->whereHas('semester', fn($q) => $q->where('is_current', true)); }
    public function hostelAllocation() { return $this->hasOne(RoomAllocation::class)->where('status', 'active'); }
    public function alumni() { return $this->hasOne(Alumni::class); }
    public function continuousAssessments() { return $this->hasMany(ContinuousAssessment::class); }
    public function examResults() { return $this->hasMany(ExamResult::class); }
    public function gradeAppeals() { return $this->hasMany(GradeAppeal::class); }
    public function holds() { return $this->hasMany(StudentHold::class); }
    public function activeHolds() { return $this->hasMany(StudentHold::class)->where('is_active', true); }
    public function waitlist() { return $this->hasMany(CourseWaitlist::class); }

    public function getHasActiveHoldAttribute(): bool
    {
        return $this->activeHolds()->where('blocks_registration', true)->exists();
    }

    // Name comes from the linked user record
    public function getFullNameAttribute() { return optional($this->user)->name ?? ''; }
    public function getNameAttribute() { return optional($this->user)->name ?? ''; }

    public function getAgeAttribute() { return $this->date_of_birth ? $this->date_of_birth->age : null; }
    public function getPhotoUrlAttribute() { return $this->photo ? asset('storage/' . $this->photo) : asset('images/default-student.png'); }

    public function getLatestGpaAttribute() { return $this->gpaRecords()->latest()->first()?->gpa ?? 0.00; }
    public function getLatestCgpaAttribute() { return $this->gpaRecords()->latest()->first()?->cgpa ?? 0.00; }

    public function scopeActive($query) { return $query->where('students.status', 'active'); }

    public function scopeByProgram($query, $programId) { return $query->where('program_id', $programId); }

    // Faculty filter through programs→departments chain
    public function scopeByFaculty($query, $facultyId) {
        return $query->whereIn('program_id',
            Program::whereIn('department_id',
                Department::where('faculty_id', $facultyId)->pluck('id')
            )->pluck('id')
        );
    }
}
