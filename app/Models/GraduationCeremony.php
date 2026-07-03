<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GraduationCeremony extends Model {
    use HasFactory;

    protected $fillable = [
        'academic_year_id', 'name', 'ceremony_date', 'venue', 'dress_code',
        'max_graduates', 'status', 'notes', 'created_by',
    ];
    protected $casts = ['ceremony_date' => 'date'];

    public function academicYear() { return $this->belongsTo(AcademicYear::class); }
    public function createdBy() { return $this->belongsTo(User::class, 'created_by'); }
    public function applications() { return $this->hasMany(GraduationApplication::class, 'ceremony_id'); }

    public static function statusColor(string $status): string {
        return match($status) {
            'planned'   => 'secondary',
            'confirmed' => 'primary',
            'completed' => 'success',
            'cancelled' => 'danger',
            default     => 'secondary',
        };
    }

    public static function statusLabel(string $status): string {
        return match($status) {
            'planned'   => 'Planned',
            'confirmed' => 'Confirmed',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            default     => ucfirst($status),
        };
    }
}
