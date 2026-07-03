<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GradeScale extends Model
{
    protected $fillable = ['grade', 'min_score', 'grade_points', 'label', 'is_pass', 'sort_order'];
    protected $casts    = ['min_score' => 'decimal:2', 'grade_points' => 'decimal:2', 'is_pass' => 'boolean'];

    // In-request cache so we don't query on every call
    private static ?array $cached = null;

    public static function clearCache(): void
    {
        static::$cached = null;
    }

    // Returns grades sorted highest min_score first — ready for a match(true) loop
    public static function orderedForLookup(): array
    {
        if (static::$cached === null) {
            static::$cached = static::orderBy('min_score', 'desc')->get()->toArray();
        }
        return static::$cached;
    }

    public static function fromScore(float $score): array
    {
        foreach (static::orderedForLookup() as $row) {
            if ($score >= $row['min_score']) return $row;
        }
        return ['grade' => 'F', 'grade_points' => 0.0, 'label' => 'Fail', 'is_pass' => false];
    }
}
