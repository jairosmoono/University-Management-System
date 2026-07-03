<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeeItem extends Model {
    use HasFactory;

    protected $fillable = ['fee_structure_id', 'fee_type', 'description', 'amount', 'is_mandatory'];
    protected $casts = ['amount' => 'decimal:2', 'is_mandatory' => 'boolean'];

    public function feeStructure() { return $this->belongsTo(FeeStructure::class); }
}
