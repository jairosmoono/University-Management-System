<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportTicket extends Model {
    use HasFactory;

    protected $fillable = ['ticket_number', 'user_id', 'subject', 'description', 'category', 'priority', 'status', 'assigned_to', 'attachment'];

    public function user() { return $this->belongsTo(User::class); }
    public function submittedBy() { return $this->belongsTo(User::class, 'user_id'); }
    public function assignedTo() { return $this->belongsTo(User::class, 'assigned_to'); }
    public function responses() { return $this->hasMany(TicketResponse::class, 'support_ticket_id'); }
    public function scopeOpen($query) { return $query->where('status', 'open'); }

    public function getPriorityColorAttribute() {
        return ['low' => 'success', 'medium' => 'info', 'high' => 'warning', 'urgent' => 'danger'][$this->priority] ?? 'secondary';
    }
}
