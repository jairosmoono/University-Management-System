<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BudgetTransaction extends Model
{
    protected $fillable = [
        'department_budget_id', 'type', 'category',
        'amount', 'description', 'reference_no', 'transaction_date', 'recorded_by',
    ];

    protected $casts = [
        'amount'           => 'decimal:2',
        'transaction_date' => 'date',
    ];

    public function budget()     { return $this->belongsTo(DepartmentBudget::class, 'department_budget_id'); }
    public function recordedBy() { return $this->belongsTo(User::class, 'recorded_by'); }
}
