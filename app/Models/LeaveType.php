<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveType extends Model {
    use HasFactory;

    protected $fillable = ['name', 'days_allowed', 'is_paid', 'description'];
    protected $casts = ['is_paid' => 'boolean'];

    public function leaveRequests() { return $this->hasMany(LeaveRequest::class); }
}
