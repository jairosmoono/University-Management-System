<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assignment extends Model {
    use HasFactory;

    protected $fillable = ['course_offering_id', 'title', 'description', 'due_date', 'total_marks', 'status'];
    protected $casts = ['due_date' => 'datetime', 'total_marks' => 'decimal:2'];

    public function courseOffering() { return $this->belongsTo(CourseOffering::class); }
    public function submissions() { return $this->hasMany(AssignmentSubmission::class); }
    public function getIsOverdueAttribute() { return $this->due_date && $this->due_date->isPast(); }
    public function getSubmissionCountAttribute() { return $this->submissions()->count(); }
}
