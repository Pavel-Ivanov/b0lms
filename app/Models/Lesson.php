<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Lesson extends Model
{
    use HasFactory;
    protected $fillable = [
        'course_id',
        'name',
        'announcement',
        'lesson_content',
        'position',
        'is_published',
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    public function scopePublished(Builder $query): void
    {
        $query->where('is_published', true);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function quizzes(): HasMany
    {
        return $this->hasMany(Quiz::class);
    }

    public function questions(): HasManyThrough
    {
        return $this->hasManyThrough(Lesson::class, Quiz::class, 'quiz_id', 'question_id', 'id', 'id');
    }

    public function getNext(): ?self
    {
        $lessons = $this->course->publishedLessons()->get();

        $currentIndex = $lessons->search(fn (Lesson $lesson) => $lesson->is($this));

        if ($currentIndex === $lessons->keys()->last()) {
            return null;
        }

        return $lessons[$currentIndex + 1];
    }

    public function getPrevious(): ?self
    {
        $lessons = $this->course->publishedLessons()->get();

        $currentIndex = $lessons->search(fn (Lesson $lesson) => $lesson->is($this));

        if ($currentIndex === 0) {
            return null;
        }

        return $lessons[$currentIndex - 1];
    }

    public function isCompleted(): bool
    {
        return auth()->user()->completedLessons->containsStrict('id', $this->id);
    }

    public function markAsCompleted(): self
    {
        if ($this->isCompleted()) {
            return $this;
        }

        auth()->user()->completeLesson($this);
        auth()->user()->refresh();

        return $this;
    }

    public function markAsUncompleted(): self
    {
        if (! $this->isCompleted()) {
            return $this;
        }

        auth()->user()->uncompleteLesson($this);
        auth()->user()->refresh();

        return $this;
    }

}
