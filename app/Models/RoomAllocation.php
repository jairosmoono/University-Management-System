<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomAllocation extends Model {
    use HasFactory;

    // Actual DB columns: student_id, hostel_room_id, allocation_date, expected_vacate_date, actual_vacate_date, status, allocated_by
    protected $fillable = [
        'student_id', 'hostel_room_id', 'allocation_date',
        'expected_vacate_date', 'actual_vacate_date', 'status', 'allocated_by',
    ];
    protected $casts = [
        'allocation_date'      => 'date',
        'expected_vacate_date' => 'date',
        'actual_vacate_date'   => 'date',
    ];

    public function hostelRoom() { return $this->belongsTo(HostelRoom::class); }
    public function student() { return $this->belongsTo(Student::class); }
    public function allocatedBy() { return $this->belongsTo(User::class, 'allocated_by'); }
}
