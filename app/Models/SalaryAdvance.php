<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalaryAdvance extends Model
{
    protected $fillable = [
        'employee_id', 'amount_requested', 'amount_approved', 'reason',
        'request_date', 'repayment_start_date', 'repayment_months', 'status', 'remarks',
    ];

    protected $casts = [
        'amount_requested'    => 'decimal:2',
        'amount_approved'     => 'decimal:2',
        'request_date'        => 'date',
        'repayment_start_date'=> 'date',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
