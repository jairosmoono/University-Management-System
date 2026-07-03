<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Admission extends Model {
    use HasFactory;

    protected $fillable = [
        'application_number', 'program_id', 'semester_id',
        'first_name', 'last_name', 'middle_name',
        'date_of_birth', 'gender', 'nationality', 'phone', 'email', 'address',
        'previous_school', 'qualification_type', 'year_completed', 'grade',
        'documents', 'status', 'rejection_reason', 'reviewed_by', 'reviewed_at', 'student_id',
    ];
    protected $casts = ['date_of_birth' => 'date', 'reviewed_at' => 'datetime', 'documents' => 'array'];

    public function program() { return $this->belongsTo(Program::class); }
    public function semester() { return $this->belongsTo(Semester::class); }
    public function reviewer() { return $this->belongsTo(User::class, 'reviewed_by'); }
    public function getFullNameAttribute() { return trim($this->first_name . ' ' . $this->last_name); }
    public function scopePending($query) { return $query->where('status', 'pending'); }
}
