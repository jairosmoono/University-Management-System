<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ResearchProject extends Model {
    use HasFactory, SoftDeletes;

    // Actual DB columns: title, abstract, principal_investigator_id, co_investigators,
    // start_date, end_date, budget, funding_source, keywords, status
    protected $fillable = [
        'title', 'abstract', 'principal_investigator_id', 'co_investigators',
        'start_date', 'end_date', 'budget', 'funding_source', 'keywords', 'status',
    ];
    protected $casts = ['start_date' => 'date', 'end_date' => 'date', 'budget' => 'decimal:2'];

    public function principalInvestigator() { return $this->belongsTo(Staff::class, 'principal_investigator_id'); }
    public function leadResearcher() { return $this->principalInvestigator(); } // alias
    public function publications() { return $this->hasMany(Publication::class, 'research_project_id'); }
}
