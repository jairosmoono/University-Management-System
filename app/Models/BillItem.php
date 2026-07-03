<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillItem extends Model {
    use HasFactory;

    // Actual DB columns: student_bill_id, fee_type, description, amount, discount
    protected $fillable = ['student_bill_id', 'fee_type', 'description', 'amount', 'discount'];
    protected $casts = ['amount' => 'decimal:2', 'discount' => 'decimal:2'];

    public function studentBill() { return $this->belongsTo(StudentBill::class); }
}
