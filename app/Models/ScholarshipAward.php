<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScholarshipAward extends Model {
    use HasFactory;

    // Actual DB columns: scholarship_id, student_id, award_date, notes, status, awarded_by
    protected $fillable = ['scholarship_id', 'student_id', 'award_date', 'notes', 'status', 'awarded_by'];
    protected $casts = ['award_date' => 'date'];

    public function scholarship() { return $this->belongsTo(Scholarship::class); }
    public function student() { return $this->belongsTo(Student::class); }
    public function awardedBy() { return $this->belongsTo(\App\Models\User::class, 'awarded_by'); }
}
