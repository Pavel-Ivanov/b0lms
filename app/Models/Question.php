<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Question extends Model
{
    use LogsActivity;

    protected $fillable = [
        'quiz_id',
        'question_text',
        'hint',
        'more_info_link',
    ];

    public function questionOptions(): HasMany
    {
        return $this->hasMany(QuestionOption::class);
    }

    public function correctQuestionOption()
    {
        return $this->questionOptions()->where('correct', true)->first();
    }

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function testAnswers(): HasMany
    {
        return $this->hasMany(TestAnswer::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable();
    }

}
