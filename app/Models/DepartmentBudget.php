<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DepartmentBudget extends Model
{
    protected $fillable = [
        'department_id', 'academic_year_id', 'fiscal_year',
        'total_budget', 'description', 'status', 'approved_by', 'approved_at',
    ];

    protected $casts = [
        'total_budget' => 'decimal:2',
        'approved_at'  => 'datetime',
    ];

    public function department()  { return $this->belongsTo(Department::class); }
    public function academicYear(){ return $this->belongsTo(AcademicYear::class); }
    public function approvedBy()  { return $this->belongsTo(User::class, 'approved_by'); }
    public function transactions(){ return $this->hasMany(BudgetTransaction::class); }

    public function getTotalExpensesAttribute(): float
    {
        return (float) $this->transactions()->where('type', 'expense')->sum('amount');
    }

    public function getTotalAllocatedAttribute(): float
    {
        return (float) $this->transactions()->where('type', 'allocation')->sum('amount');
    }

    public function getRemainingBudgetAttribute(): float
    {
        $credits = $this->transactions()->whereIn('type', ['allocation', 'adjustment'])->sum('amount');
        $debits  = $this->transactions()->where('type', 'expense')->sum('amount');
        return (float)$this->total_budget + $credits - $debits;
    }

    public function getUsedPercentAttribute(): float
    {
        if ($this->total_budget <= 0) return 0;
        return round(($this->total_expenses / $this->total_budget) * 100, 1);
    }
}
