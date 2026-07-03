<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model {
    protected $fillable = ['user_id', 'action', 'url', 'model_type', 'model_id', 'old_values', 'new_values', 'request_data', 'ip_address', 'user_agent'];
    protected $casts = ['old_values' => 'array', 'new_values' => 'array', 'request_data' => 'array'];
    public $timestamps = false;
    protected $attributes = ['created_at' => null];

    public function user() { return $this->belongsTo(User::class); }
}
