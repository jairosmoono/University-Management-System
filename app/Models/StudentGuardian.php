<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentGuardian extends Model {
    use HasFactory;

    // Actual DB columns: student_id, name, relationship, phone, email, address, is_emergency_contact
    protected $fillable = ['student_id', 'name', 'relationship', 'phone', 'email', 'address', 'is_emergency_contact'];
    protected $casts = ['is_emergency_contact' => 'boolean'];

    public function student() { return $this->belongsTo(Student::class); }
}
