<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResearchPaper extends Model
{
    use HasFactory;

    protected $fillable = [
        'research_project_id', 'uploaded_by', 'title', 'authors', 'abstract',
        'keywords', 'file_path', 'file_original_name', 'file_size', 'file_mime',
        'category', 'publication_year', 'doi', 'is_public',
    ];

    protected $casts = ['is_public' => 'boolean'];

    public function researchProject()
    {
        return $this->belongsTo(ResearchProject::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getFileSizeFormattedAttribute(): string
    {
        if (!$this->file_size) return '—';
        $bytes = $this->file_size;
        if ($bytes >= 1_048_576) return number_format($bytes / 1_048_576, 1) . ' MB';
        if ($bytes >= 1_024)     return number_format($bytes / 1_024, 1) . ' KB';
        return $bytes . ' B';
    }

    public static function categoryLabel(string $category): string
    {
        return match($category) {
            'journal_article'  => 'Journal Article',
            'conference_paper' => 'Conference Paper',
            'thesis'           => 'Thesis',
            'technical_report' => 'Technical Report',
            'book_chapter'     => 'Book Chapter',
            default            => 'Other',
        };
    }
}
