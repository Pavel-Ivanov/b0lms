<?php

namespace App\Models;

use App\Enums\StepType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class EnrollmentStep extends Model
{
//    use LogsActivity;

    protected $fillable = [
        'enrollment_id',
        'course_id',
        'user_id',
        'stepable_id',
        'stepable_type',
        'position',
        'is_completed',
        'is_enabled',
        'started_at',
        'completed_at',
        'max_attempts',
        'passing_percentage',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'is_enabled' => 'boolean',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'max_attempts' => 'integer',
        'passing_percentage' => 'integer',
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

    public function tests(): HasMany
    {
        return $this->hasMany(Test::class);
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

    public function isEnabled(): bool
    {
        return $this->is_enabled;
    }

    public function enable(): self
    {
        if ($this->is_enabled) {
            return $this;
        }
        $this->is_enabled = true;
        return $this;
    }

    public function disable(): self
    {
        if (!$this->is_enabled) {
            return $this;
        }
        $this->is_enabled = false;
        return $this;
    }

/*    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable();
    }*/

}
