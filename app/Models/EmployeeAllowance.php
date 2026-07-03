<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeAllowance extends Model
{
    protected $fillable = [
        'employee_id', 'allowance_type', 'description', 'amount', 'percentage', 'is_active',
    ];

    protected $casts = [
        'amount'     => 'decimal:2',
        'percentage' => 'decimal:2',
        'is_active'  => 'boolean',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    /** Compute the actual ZMW value given the employee's basic salary */
    public function resolveAmount(float $basicSalary): float
    {
        if ($this->percentage > 0) {
            return round($basicSalary * ($this->percentage / 100), 2);
        }
        return (float) $this->amount;
    }
}
