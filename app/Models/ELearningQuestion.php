<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ELearningQuestion extends Model {
    use HasFactory;

    protected $table    = 'elearning_questions';
    protected $fillable = ['quiz_id', 'question_text', 'question_type', 'marks', 'sort_order'];

    public function quiz()    { return $this->belongsTo(ELearningQuiz::class, 'quiz_id'); }
    public function options() { return $this->hasMany(ELearningQuestionOption::class, 'question_id')->orderBy('sort_order'); }

    public function getCorrectOptionAttribute(): ?ELearningQuestionOption {
        return $this->options()->where('is_correct', true)->first();
    }
}
