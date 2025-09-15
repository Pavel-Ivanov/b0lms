<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class QuestionOption extends Model
{
    use LogsActivity;

    protected $fillable = [
        'question_id',
        'option',
        'rationale',
        'correct',
    ];

    protected function casts(): array
    {
        return [
            'correct' => 'boolean',
        ];
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
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
