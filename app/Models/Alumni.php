<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alumni extends Model {
    use HasFactory;

    protected $table = 'alumni';

    // Actual DB columns: student_id, graduation_year, current_employer, job_title,
    // employment_status, city, country, linkedin_url, biography
    protected $fillable = [
        'student_id', 'graduation_year', 'current_employer', 'job_title',
        'employment_status', 'city', 'country', 'linkedin_url', 'biography',
    ];

    public function student() { return $this->belongsTo(Student::class); }
}
