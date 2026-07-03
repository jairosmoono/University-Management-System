<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeDeduction extends Model
{
    protected $fillable = [
        'employee_id', 'deduction_type', 'description', 'amount', 'is_recurring', 'is_active',
    ];

    protected $casts = [
        'amount'       => 'decimal:2',
        'is_recurring' => 'boolean',
        'is_active'    => 'boolean',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
