<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quiz extends Model
{
    protected $attributes = [
        'passing_percentage' => 80,
        'max_attempts' => 3,
    ];
    protected $fillable = [
        'lesson_id',
        'name',
        'description',
        'is_published',
        'passing_percentage',
        'max_attempts',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'passing_percentage' => 'integer',
            'max_attempts' => 'integer',
        ];
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }


    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

}
