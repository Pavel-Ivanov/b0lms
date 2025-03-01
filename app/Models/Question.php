<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Question extends Model
{
    protected $fillable = [
        'course_id',
        'question_text',
        'answer_explanation',
        'more_info_link',
    ];

/*    public function questionOptions(): HasMany
    {
        return $this->hasMany(QuestionOption::class);
    }*/

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
