<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamType extends Model
{
    protected $fillable = ['name', 'code', 'category', 'description', 'is_active', 'sort_order'];
    protected $casts    = ['is_active' => 'boolean'];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Category helpers
    public function isCa(): bool   { return $this->category === 'ca'; }
    public function isExam(): bool { return $this->category === 'exam'; }
}
