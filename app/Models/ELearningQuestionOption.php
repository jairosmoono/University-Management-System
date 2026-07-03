<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ELearningQuestionOption extends Model {
    use HasFactory;

    protected $table    = 'elearning_question_options';
    protected $fillable = ['question_id', 'option_text', 'is_correct', 'sort_order'];
    protected $casts    = ['is_correct' => 'boolean'];

    public function question() { return $this->belongsTo(ELearningQuestion::class, 'question_id'); }
}
