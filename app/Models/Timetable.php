<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Timetable extends Model {
    use HasFactory;

    protected $fillable = ['course_offering_id', 'day_of_week', 'start_time', 'end_time', 'room', 'type'];

    public function courseOffering() { return $this->belongsTo(CourseOffering::class); }

    public function getDayLabelAttribute() {
        $days = ['monday' => 'Monday', 'tuesday' => 'Tuesday', 'wednesday' => 'Wednesday', 'thursday' => 'Thursday', 'friday' => 'Friday', 'saturday' => 'Saturday'];
        return $days[$this->day_of_week] ?? ucfirst($this->day_of_week);
    }
}
