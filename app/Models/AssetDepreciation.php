<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetDepreciation extends Model {
    use HasFactory;

    protected $table = 'asset_depreciation_logs';

    protected $fillable = [
        'asset_id', 'period_label', 'method',
        'opening_value', 'depreciation_amount', 'closing_value',
        'notes', 'recorded_by',
    ];

    protected $casts = [
        'opening_value'        => 'decimal:2',
        'depreciation_amount'  => 'decimal:2',
        'closing_value'        => 'decimal:2',
    ];

    public function asset()      { return $this->belongsTo(Asset::class); }
    public function recordedBy() { return $this->belongsTo(User::class, 'recorded_by'); }
}
