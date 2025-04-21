<?php

namespace App\Models;

use App\Enums\StepType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EnrollmentStep extends Model
{
    protected $fillable = [
        'enrollment_id',
        'course_id',
        'user_id',
        'stepable_id',
        'stepable_type',
        'position',
        'is_completed',
        'started_at',
        'completed_at'
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];


    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function stepableType(): string
    {
        return class_basename($this->stepable_type);
    }

    public function stepableModel(): ?Model
    {
        return $this->stepable_type::findOrFail($this->stepable_id);
    }

    public function isCompleted(): bool
    {
        return $this->is_completed;
    }

    public function markAsCompleted(): self
    {
        if ($this->is_completed) {
            return $this;
        }

        $this->is_completed = true;

        return $this;
    }

    public function markAsUnCompleted(): self
    {
        if (!$this->is_completed) {
            return $this;
        }

        $this->is_completed = false;

        return $this;
    }
}
