<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseWaitlist extends Model
{
    protected $table = 'course_waitlist';

    protected $fillable = ['student_id', 'course_offering_id', 'position', 'status', 'notified_at'];

    protected $casts = ['notified_at' => 'datetime'];

    public function student()       { return $this->belongsTo(Student::class); }
    public function courseOffering(){ return $this->belongsTo(CourseOffering::class); }
}
