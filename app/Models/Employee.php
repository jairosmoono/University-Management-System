<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model {
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'employee_id', 'department_id', 'designation', 'employment_type',
        'join_date', 'contract_end_date', 'basic_salary', 'bank_name', 'bank_account',
        'sort_code', 'bank_branch', 'national_id', 'status',
    ];
    protected $casts = [
        'basic_salary'      => 'decimal:2',
        'join_date'         => 'date',
        'contract_end_date' => 'date',
    ];
    protected $appends = ['full_name'];

    public function user() { return $this->belongsTo(User::class); }
    public function department() { return $this->belongsTo(Department::class); }
    public function leaveRequests() { return $this->hasMany(LeaveRequest::class); }
    public function payrolls() { return $this->hasMany(Payroll::class); }
    public function allowances() { return $this->hasMany(EmployeeAllowance::class); }
    public function deductions() { return $this->hasMany(EmployeeDeduction::class); }
    public function salaryAdvances() { return $this->hasMany(SalaryAdvance::class); }
    public function documents()     { return $this->hasMany(EmployeeDocument::class); }

    // Name comes from the linked user record
    public function getFullNameAttribute() { return optional($this->user)->name ?? ''; }
    public function getNameAttribute() { return optional($this->user)->name ?? ''; }

    public function scopeActive($query) { return $query->where('status', 'active'); }
}
