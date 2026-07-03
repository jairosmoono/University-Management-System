<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Scholarship extends Model {
    use HasFactory;

    protected $fillable = ['name', 'type', 'description', 'coverage_type', 'coverage_value', 'max_recipients', 'status'];
    protected $casts = ['coverage_value' => 'decimal:2'];

    public function awards() { return $this->hasMany(ScholarshipAward::class); }
    public function scopeActive($query) { return $query->where('status', 'active'); }
}
