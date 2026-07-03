<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceSession extends Model {
    use HasFactory;

    protected $fillable = ['course_offering_id', 'program_id', 'date', 'session_type', 'topic', 'created_by'];
    protected $casts = ['date' => 'date'];

    public function courseOffering() { return $this->belongsTo(CourseOffering::class); }
    public function program() { return $this->belongsTo(Program::class); }
    public function records() { return $this->hasMany(AttendanceRecord::class); }
    public function createdBy() { return $this->belongsTo(Staff::class, 'created_by'); }

    public function getPresentCountAttribute() { return $this->records()->where('status', 'present')->count(); }
    public function getAbsentCountAttribute() { return $this->records()->where('status', 'absent')->count(); }
}
