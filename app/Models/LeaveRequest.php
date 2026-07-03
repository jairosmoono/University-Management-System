<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveRequest extends Model {
    use HasFactory;

    // Actual DB columns: employee_id, leave_type_id, start_date, end_date, reason, attachment, status, approved_by, remarks
    protected $fillable = ['employee_id', 'leave_type_id', 'start_date', 'end_date', 'reason', 'attachment', 'status', 'approved_by', 'remarks'];
    protected $casts = ['start_date' => 'date', 'end_date' => 'date'];

    public function employee() { return $this->belongsTo(Employee::class); }
    public function leaveType() { return $this->belongsTo(LeaveType::class); }
    public function approvedBy() { return $this->belongsTo(User::class, 'approved_by'); }

    public function getDaysRequestedAttribute() {
        return $this->start_date && $this->end_date
            ? $this->start_date->diffInDays($this->end_date) + 1
            : 0;
    }
}
