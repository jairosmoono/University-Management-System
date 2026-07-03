<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CoursePrerequisite extends Model
{
    protected $fillable = ['course_id', 'prerequisite_course_id', 'min_grade'];

    public function course()            { return $this->belongsTo(Course::class); }
    public function prerequisiteCourse(){ return $this->belongsTo(Course::class, 'prerequisite_course_id'); }
}
