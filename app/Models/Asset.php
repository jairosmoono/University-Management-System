<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Asset extends Model {
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'department_id', 'name', 'asset_code', 'category', 'description',
        'serial_number', 'purchase_date', 'purchase_price', 'current_value',
        'location', 'warranty_expiry', 'status',
        'depreciation_method', 'depreciation_rate', 'useful_life_years', 'salvage_value',
    ];

    protected $casts = [
        'purchase_date'   => 'date',
        'warranty_expiry' => 'date',
        'purchase_price'  => 'decimal:2',
        'current_value'   => 'decimal:2',
        'salvage_value'   => 'decimal:2',
        'depreciation_rate' => 'decimal:2',
    ];

    public function department() { return $this->belongsTo(Department::class); }
    public function depreciationLogs() { return $this->hasMany(AssetDepreciation::class)->latest(); }

    // Annual depreciation amount based on configured method
    public function annualDepreciation(): float
    {
        $purchase = (float) ($this->purchase_price ?? 0);
        $current  = (float) ($this->current_value  ?? $purchase);
        $salvage  = (float) ($this->salvage_value  ?? 0);

        if ($this->depreciation_method === 'straight_line' && $this->useful_life_years > 0) {
            return max(0, ($purchase - $salvage) / $this->useful_life_years);
        }

        if ($this->depreciation_method === 'declining_balance' && $this->depreciation_rate > 0) {
            return $current * ($this->depreciation_rate / 100);
        }

        return 0;
    }

    public function monthlyDepreciation(): float
    {
        return $this->annualDepreciation() / 12;
    }

    // Total depreciation posted in logs
    public function totalDepreciationPosted(): float
    {
        return (float) $this->depreciationLogs()->sum('depreciation_amount');
    }

    // Accumulated depreciation = original cost - current book value
    public function accumulatedDepreciation(): float
    {
        $purchase = (float) ($this->purchase_price ?? 0);
        $current  = (float) ($this->current_value  ?? $purchase);
        return max(0, $purchase - $current);
    }

    // % of value depreciated so far
    public function depreciationPercent(): float
    {
        $purchase = (float) ($this->purchase_price ?? 0);
        if ($purchase <= 0) return 0;
        return round($this->accumulatedDepreciation() / $purchase * 100, 1);
    }

    // Estimated remaining useful life in years (straight-line only)
    public function remainingLife(): ?float
    {
        if ($this->depreciation_method !== 'straight_line' || !$this->purchase_date) return null;
        $yearsUsed = $this->purchase_date->diffInYears(now());
        return max(0, ($this->useful_life_years ?? 0) - $yearsUsed);
    }
}
