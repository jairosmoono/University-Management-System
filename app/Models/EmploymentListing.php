<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmploymentListing extends Model
{
    protected $fillable = [
        'title', 'department_id', 'description', 'requirements',
        'employment_type', 'vacancies', 'deadline', 'status',
    ];

    protected $casts = ['deadline' => 'date'];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
