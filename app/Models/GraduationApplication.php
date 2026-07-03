<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GraduationApplication extends Model {
    use HasFactory;

    protected $fillable = [
        'student_id', 'program_id', 'academic_year_id', 'ceremony_id',
        'cgpa', 'credits_earned', 'status',
        'finance_cleared', 'library_cleared', 'academic_cleared', 'cleared_at',
        'approved_by', 'approved_at', 'graduation_date',
        'rejection_reason', 'notes',
    ];
    protected $casts = [
        'finance_cleared'  => 'boolean',
        'library_cleared'  => 'boolean',
        'academic_cleared' => 'boolean',
        'cleared_at'       => 'datetime',
        'approved_at'      => 'datetime',
        'graduation_date'  => 'date',
        'cgpa'             => 'decimal:2',
    ];

    public function student()     { return $this->belongsTo(Student::class); }
    public function program()     { return $this->belongsTo(Program::class); }
    public function academicYear(){ return $this->belongsTo(AcademicYear::class); }
    public function ceremony()    { return $this->belongsTo(GraduationCeremony::class, 'ceremony_id'); }
    public function approvedBy()  { return $this->belongsTo(User::class, 'approved_by'); }

    public function isFullyCleared(): bool {
        return $this->finance_cleared && $this->library_cleared && $this->academic_cleared;
    }

    public static function statusColor(string $status): string {
        return match($status) {
            'pending'      => 'warning',
            'under_review' => 'info',
            'cleared'      => 'primary',
            'approved'     => 'success',
            'rejected'     => 'danger',
            'graduated'    => 'dark',
            default        => 'secondary',
        };
    }

    public static function statusLabel(string $status): string {
        return match($status) {
            'pending'      => 'Pending',
            'under_review' => 'Under Review',
            'cleared'      => 'Cleared',
            'approved'     => 'Approved',
            'rejected'     => 'Rejected',
            'graduated'    => 'Graduated',
            default        => ucfirst($status),
        };
    }
}
