<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payroll extends Model {
    use HasFactory;

    protected $table = 'payroll';

    // Actual DB columns: employee_id, month, year, basic_salary, allowances, deductions, tax, net_pay, payment_date, status, notes, processed_by
    protected $fillable = ['employee_id', 'month', 'year', 'basic_salary', 'allowances', 'deductions', 'tax', 'net_pay', 'payment_date', 'status', 'notes', 'processed_by'];
    protected $casts = ['basic_salary' => 'decimal:2', 'allowances' => 'decimal:2', 'deductions' => 'decimal:2', 'tax' => 'decimal:2', 'net_pay' => 'decimal:2', 'payment_date' => 'date'];

    public function employee() { return $this->belongsTo(Employee::class); }
    public function processedBy() { return $this->belongsTo(User::class, 'processed_by'); }

    public function getMonthNameAttribute() {
        return date('F', mktime(0, 0, 0, $this->month, 1));
    }
}
