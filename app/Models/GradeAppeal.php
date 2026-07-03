<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GradeAppeal extends Model
{
    protected $fillable = [
        'student_id', 'course_offering_id', 'final_result_id',
        'reason', 'supporting_document', 'status', 'admin_notes',
        'original_grade', 'original_total', 'revised_grade', 'revised_total',
        'reviewed_by', 'reviewed_at',
    ];

    protected $casts = [
        'reviewed_at'    => 'datetime',
        'original_total' => 'decimal:2',
        'revised_total'  => 'decimal:2',
    ];

    public function student()       { return $this->belongsTo(Student::class); }
    public function courseOffering(){ return $this->belongsTo(CourseOffering::class); }
    public function finalResult()   { return $this->belongsTo(FinalResult::class); }
    public function reviewedBy()    { return $this->belongsTo(User::class, 'reviewed_by'); }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'pending'      => '<span class="badge bg-warning text-dark">Pending</span>',
            'under_review' => '<span class="badge bg-info text-dark">Under Review</span>',
            'approved'     => '<span class="badge bg-success">Approved</span>',
            'rejected'     => '<span class="badge bg-danger">Rejected</span>',
            default        => '<span class="badge bg-secondary">Unknown</span>',
        };
    }
}
