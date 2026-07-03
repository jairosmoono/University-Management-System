<?php
namespace App\Models;
use App\Services\NotificationService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model {
    use HasFactory;

    protected $fillable = ['user_id', 'type', 'title', 'message', 'data', 'action_url', 'is_read', 'read_at'];
    protected $casts = ['data' => 'array', 'is_read' => 'boolean', 'read_at' => 'datetime'];

    public function user() { return $this->belongsTo(User::class); }
    public function scopeUnread($query) { return $query->where('is_read', false); }

    public static function send(int $userId, string $type, string $title, string $message, array $data = [], ?string $actionUrl = null): static {
        return NotificationService::send($userId, $type, $title, $message, $data, $actionUrl);
    }
}
