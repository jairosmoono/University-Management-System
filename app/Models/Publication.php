<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Publication extends Model {
    use HasFactory;

    protected $fillable = ['research_project_id', 'title', 'authors', 'journal_name', 'volume', 'issue', 'pages', 'year', 'doi', 'isbn', 'type'];

    public function researchProject() { return $this->belongsTo(ResearchProject::class); }
}
