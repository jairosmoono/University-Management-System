<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model {
    use HasFactory;

    protected $fillable = ['sender_id', 'receiver_id', 'subject', 'content', 'is_read', 'read_at', 'parent_id', 'sender_deleted', 'receiver_deleted', 'attachment'];
    protected $casts = ['is_read' => 'boolean', 'read_at' => 'datetime', 'sender_deleted' => 'boolean', 'receiver_deleted' => 'boolean'];

    public function sender() { return $this->belongsTo(User::class, 'sender_id'); }
    public function receiver() { return $this->belongsTo(User::class, 'receiver_id'); }
    public function parent() { return $this->belongsTo(Message::class, 'parent_id'); }
    public function replies() { return $this->hasMany(Message::class, 'parent_id'); }
    public function markAsRead() { $this->update(['is_read' => true, 'read_at' => now()]); }
}
