<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentHold extends Model
{
    protected $fillable = [
        'student_id', 'type', 'reason', 'blocks_registration',
        'placed_by', 'released_by', 'released_at', 'is_active',
    ];

    protected $casts = [
        'blocks_registration' => 'boolean',
        'is_active'           => 'boolean',
        'released_at'         => 'datetime',
    ];

    public function student()    { return $this->belongsTo(Student::class); }
    public function placedBy()   { return $this->belongsTo(User::class, 'placed_by'); }
    public function releasedBy() { return $this->belongsTo(User::class, 'released_by'); }

    public static function typeLabel(string $type): string
    {
        return match($type) {
            'financial'      => 'Financial',
            'academic'       => 'Academic',
            'disciplinary'   => 'Disciplinary',
            'library'        => 'Library',
            'hostel'         => 'Hostel',
            'administrative' => 'Administrative',
            default          => ucfirst($type),
        };
    }

    public static function typeBadgeClass(string $type): string
    {
        return match($type) {
            'financial'    => 'bg-danger',
            'academic'     => 'bg-warning text-dark',
            'disciplinary' => 'bg-dark',
            'library'      => 'bg-info text-dark',
            'hostel'       => 'bg-secondary',
            default        => 'bg-primary',
        };
    }
}
