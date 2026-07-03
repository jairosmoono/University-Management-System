<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeeStructure extends Model {
    use HasFactory;

    // Actual DB columns: name, academic_year_id, semester_id, program_id, admission_type, total_amount, status
    protected $fillable = ['program_id', 'academic_year_id', 'semester_id', 'name', 'admission_type', 'total_amount', 'status'];
    protected $casts = ['total_amount' => 'decimal:2'];

    public function program() { return $this->belongsTo(Program::class); }
    public function academicYear() { return $this->belongsTo(AcademicYear::class); }
    public function semester() { return $this->belongsTo(Semester::class); }
    public function feeItems() { return $this->hasMany(\App\Models\FeeItem::class); }
    public function studentBills() { return $this->hasMany(StudentBill::class); }
    public function scopeActive($query) { return $query->where('status', 'active'); }
}
