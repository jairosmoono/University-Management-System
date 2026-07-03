<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable {
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes;

    protected $fillable = ['name', 'email', 'password', 'phone', 'address', 'avatar', 'is_active', 'last_login_at', 'last_login_ip', 'two_factor_secret', 'two_factor_recovery_codes'];
    protected $hidden = ['password', 'remember_token', 'two_factor_secret', 'two_factor_recovery_codes'];
    protected $casts = ['email_verified_at' => 'datetime', 'password' => 'hashed', 'is_active' => 'boolean', 'last_login_at' => 'datetime'];

    public function student() { return $this->hasOne(Student::class); }
    public function staff() { return $this->hasOne(Staff::class); }
    public function employee() { return $this->hasOne(Employee::class); }
    public function sentMessages() { return $this->hasMany(Message::class, 'sender_id'); }
    public function receivedMessages() { return $this->hasMany(Message::class, 'receiver_id'); }
    public function notifications() { return $this->hasMany(Notification::class); }
    public function announcements() { return $this->hasMany(Announcement::class, 'published_by'); }
    public function documents() { return $this->hasMany(Document::class, 'uploaded_by'); }
    public function tickets() { return $this->hasMany(SupportTicket::class); }

    public function getAvatarUrlAttribute() {
        return $this->avatar ? asset('storage/' . $this->avatar) : asset('images/default-avatar.png');
    }

    public function getFullRoleAttribute() {
        return $this->getRoleNames()->first() ?? 'No Role';
    }

    public function getUnreadNotificationsCountAttribute() {
        return $this->notifications()->where('is_read', false)->count();
    }
}
