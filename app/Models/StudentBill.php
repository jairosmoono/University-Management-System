<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentBill extends Model {
    use HasFactory;

    // Actual DB columns: student_id, fee_structure_id, academic_year_id, semester_id,
    // total_amount, amount_paid, balance, due_date, status
    protected $fillable = ['student_id', 'fee_structure_id', 'academic_year_id', 'semester_id', 'total_amount', 'amount_paid', 'balance', 'due_date', 'status'];
    protected $casts = ['total_amount' => 'decimal:2', 'amount_paid' => 'decimal:2', 'balance' => 'decimal:2', 'due_date' => 'date'];

    public function student() { return $this->belongsTo(Student::class); }
    public function feeStructure() { return $this->belongsTo(FeeStructure::class); }
    public function academicYear() { return $this->belongsTo(AcademicYear::class); }
    public function semester() { return $this->belongsTo(Semester::class); }
    public function items() { return $this->hasMany(BillItem::class); }
    public function payments() { return $this->hasMany(Payment::class); }

    public function recalculate() {
        $paid = $this->payments()->where('status', 'verified')->sum('amount');
        $this->amount_paid = $paid;
        $this->balance = max(0, $this->total_amount - $paid);
        $this->status = $this->balance <= 0 ? 'paid' : ($paid > 0 ? 'partial' : 'unpaid');
        $this->save();
    }
}
