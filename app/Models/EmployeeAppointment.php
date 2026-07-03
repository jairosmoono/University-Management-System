<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeAppointment extends Model
{
    protected $fillable = [
        'employee_id', 'department_id', 'position', 'appointment_date',
        'start_date', 'end_date', 'salary', 'contract_type', 'notes', 'status',
    ];

    protected $casts = [
        'appointment_date' => 'date',
        'start_date'       => 'date',
        'end_date'         => 'date',
        'salary'           => 'decimal:2',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
