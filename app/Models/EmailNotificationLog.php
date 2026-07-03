<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class EmailNotificationLog extends Model
{
    protected $table = 'email_notification_logs';

    protected $fillable = [
        'subject', 'body', 'attachments', 'recipient_type', 'recipient_filter',
        'sent_count', 'failed_count', 'sent_user_ids', 'failed_user_ids',
        'status', 'sent_by',
    ];

    protected $casts = [
        'recipient_filter' => 'array',
        'attachments'      => 'array',
        'sent_user_ids'    => 'array',
        'failed_user_ids'  => 'array',
    ];

    public function sender() { return $this->belongsTo(User::class, 'sent_by'); }

    public function getRecipientLabelAttribute(): string
    {
        return match($this->recipient_type) {
            'all'        => 'All Users',
            'students'   => 'All Students',
            'staff'      => 'All Staff',
            'department' => 'Department: ' . (Department::find($this->recipient_filter['department_id'] ?? 0)?->name ?? '—'),
            'role'       => 'Role: ' . ucfirst(str_replace('-', ' ', $this->recipient_filter['role'] ?? '')),
            default      => ucfirst($this->recipient_type),
        };
    }
}
