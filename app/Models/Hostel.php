<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hostel extends Model {
    use HasFactory;

    // Actual DB columns: name, type (male/female/mixed), warden_id, location, description, status
    protected $fillable = ['name', 'type', 'warden_id', 'location', 'description', 'status'];

    public function rooms() { return $this->hasMany(HostelRoom::class); }
    public function availableRooms() { return $this->hasMany(HostelRoom::class)->where('status', 'available'); }
    public function warden() { return $this->belongsTo(Staff::class, 'warden_id'); }
    public function getOccupancyAttribute() { return $this->rooms()->sum('occupied'); }
    public function getTotalCapacityAttribute() { return $this->rooms()->sum('capacity'); }
    public function getOccupancyRateAttribute() {
        $capacity = $this->total_capacity;
        return $capacity > 0 ? round(($this->occupancy / $capacity) * 100, 1) : 0;
    }
}
