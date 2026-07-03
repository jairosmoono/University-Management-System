<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class NotificationPreference extends Model
{
    protected $fillable = ['user_id', 'type', 'email_enabled', 'sms_enabled'];
    protected $casts    = ['email_enabled' => 'boolean', 'sms_enabled' => 'boolean'];

    public function user() { return $this->belongsTo(User::class); }

    // All configurable notification types
    public static function allTypes(): array
    {
        return [
            'announcement' => ['label' => 'Announcements',        'icon' => 'bi-megaphone',    'desc' => 'New announcements published by admin'],
            'result'       => ['label' => 'Academic Results',     'icon' => 'bi-bar-chart',    'desc' => 'When your exam results are released'],
            'leave'        => ['label' => 'Leave Requests',       'icon' => 'bi-calendar-x',   'desc' => 'Approval or rejection of leave applications'],
            'payment'      => ['label' => 'Payments & Finance',   'icon' => 'bi-credit-card',  'desc' => 'Payment receipts, salary advances'],
            'admission'    => ['label' => 'Admissions',           'icon' => 'bi-person-plus',  'desc' => 'Application status updates'],
            'support'      => ['label' => 'Support Tickets',      'icon' => 'bi-headset',      'desc' => 'Replies to your support requests'],
            'general'      => ['label' => 'General Notifications','icon' => 'bi-bell',         'desc' => 'Other system notifications'],
        ];
    }
}
