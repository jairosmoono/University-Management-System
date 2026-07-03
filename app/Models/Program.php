<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Program extends Model {
    use HasFactory, SoftDeletes;

    // Actual DB columns: department_id, name, code, level, duration_years, credit_hours_required, description, status
    protected $fillable = ['department_id', 'name', 'code', 'level', 'duration_years', 'duration_unit', 'credit_hours_required', 'description', 'status'];

    public function department() { return $this->belongsTo(Department::class); }
    public function students() { return $this->hasMany(Student::class); }
    public function courses() { return $this->belongsToMany(Course::class, 'course_program')->withPivot('year_of_study', 'is_mandatory'); }
    public function feeStructures() { return $this->hasMany(FeeStructure::class); }
    public function admissions() { return $this->hasMany(Admission::class); }
    public function scopeActive($query) { return $query->where('status', 'active'); }

    // Faculty is accessed through department (no direct faculty_id on programs table)
    public function getFacultyAttribute() { return $this->department?->faculty; }

    public function getDurationLabelAttribute(): string
    {
        if (!$this->duration_years) return '—';
        $val  = $this->duration_years;
        $unit = $this->duration_unit ?? 'years';
        $word = $unit === 'months'
            ? ($val === 1 ? 'Month' : 'Months')
            : ($val === 1 ? 'Year'  : 'Years');
        return "{$val} {$word}";
    }

    public function getLevelLabelAttribute(): string
    {
        return self::levelLabels()[$this->level] ?? ucfirst(str_replace('_', ' ', $this->level ?? ''));
    }

    public static function levelLabels(): array
    {
        return [
            'degree'               => 'Degree',
            'diploma'              => 'Diploma',
            'certificate'          => 'Certificate',
            'craft_certificate'    => 'Craft Certificate',
            'trade_test_certificate' => 'Trade Test Certificate',
        ];
    }
}
