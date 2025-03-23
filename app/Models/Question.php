<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    protected $fillable = [
        'quiz_id',
        'question_text',
        'answer_explanation',
        'more_info_link',
    ];

    public function questionOptions(): HasMany
    {
        return $this->hasMany(QuestionOption::class);
    }

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }
}
