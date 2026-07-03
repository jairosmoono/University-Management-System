<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model {
    use HasFactory;

    // Actual DB columns: student_bill_id, reference_number, amount, payment_method,
    // transaction_reference, payment_date, notes, status, recorded_by, verified_by
    protected $fillable = [
        'student_bill_id', 'reference_number', 'amount', 'payment_method',
        'transaction_reference', 'payment_date', 'notes', 'status', 'recorded_by', 'verified_by',
    ];
    protected $casts = ['amount' => 'decimal:2', 'payment_date' => 'date'];

    public function studentBill() { return $this->belongsTo(StudentBill::class); }
    public function student() { return $this->hasOneThrough(Student::class, StudentBill::class, 'id', 'id', 'student_bill_id', 'student_id'); }
    public function recordedBy() { return $this->belongsTo(User::class, 'recorded_by'); }
    public function verifiedBy() { return $this->belongsTo(User::class, 'verified_by'); }

    protected static function booted() {
        static::created(function ($payment) {
            if ($payment->student_bill_id) {
                $payment->studentBill?->recalculate();
            }
        });
        static::updated(function ($payment) {
            if ($payment->student_bill_id) {
                $payment->studentBill?->recalculate();
            }
        });
    }
}
