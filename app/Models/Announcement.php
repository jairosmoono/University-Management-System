<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model {
    use HasFactory;

    protected $fillable = ['user_id', 'title', 'content', 'category', 'priority', 'target_audience', 'published_by', 'is_published', 'published_at', 'expiry_date', 'attachments', 'send_email', 'send_sms', 'views_count'];
    protected $casts = ['target_audience' => 'array', 'attachments' => 'array', 'is_published' => 'boolean', 'send_email' => 'boolean', 'send_sms' => 'boolean', 'published_at' => 'datetime', 'expiry_date' => 'date'];

    public function author() { return $this->belongsTo(User::class, 'user_id'); }
    public function publisher() { return $this->belongsTo(User::class, 'published_by'); }
    public function scopePublished($query) { return $query->where('is_published', true); }
    public function scopeActive($query) {
        return $query->where('is_published', true)
            ->where(function ($q) { $q->whereNull('expiry_date')->orWhere('expiry_date', '>=', now()); });
    }
}
