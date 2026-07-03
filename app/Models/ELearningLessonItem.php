<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ELearningLessonItem extends Model {
    use HasFactory;

    protected $table    = 'elearning_lesson_items';
    protected $fillable = ['lesson_id', 'title', 'content_type', 'content', 'sort_order'];

    public function lesson() { return $this->belongsTo(ELearningLesson::class, 'lesson_id'); }

    public static function typeIcon(string $type): string {
        return match($type) {
            'video_url'     => 'bi-play-circle-fill text-danger',
            'pdf_upload'    => 'bi-file-earmark-pdf-fill text-danger',
            'text_html'     => 'bi-file-text-fill text-primary',
            'external_link' => 'bi-link-45deg text-info',
            default         => 'bi-file-earmark',
        };
    }

    public static function typeLabel(string $type): string {
        return match($type) {
            'video_url'     => 'Video',
            'pdf_upload'    => 'PDF',
            'text_html'     => 'Reading',
            'external_link' => 'Link',
            default         => 'Content',
        };
    }
}
