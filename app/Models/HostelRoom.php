<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HostelRoom extends Model {
    use HasFactory;

    // Actual DB columns: hostel_id, room_number, floor, room_type, capacity, amenities, status
    protected $fillable = ['hostel_id', 'room_number', 'floor', 'room_type', 'capacity', 'amenities', 'status'];
    protected $casts = ['amenities' => 'array'];

    public function hostel() { return $this->belongsTo(Hostel::class); }
    public function allocations() { return $this->hasMany(RoomAllocation::class); }
    public function activeAllocations() { return $this->hasMany(RoomAllocation::class)->where('status', 'active'); }

    public function getAvailableBedsAttribute() {
        return max(0, $this->capacity - $this->activeAllocations()->count());
    }

    public function getIsAvailableAttribute() {
        return $this->status === 'available' && $this->available_beds > 0;
    }
}
