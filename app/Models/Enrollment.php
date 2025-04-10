<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class Enrollment extends Model
{
    protected $fillable = [
        'course_id',
        'user_id',
        'enrollment_date',
        'completion_deadline',
        'is_steps_created',
    ];

    protected function casts(): array
    {
        return [
            'enrollment_date' => 'datetime',
            'completion_deadline' => 'date',
            'is_steps_created' => 'boolean',
        ];
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function steps()
    {
        return $this->hasMany(EnrollmentStep::class);
    }

    public function hasSteps(): bool
    {
        return (bool) $this->is_steps_created;
    }


    public function enrollmentInfo(): string
    {
        return $this->user->name . ' / ' . Carbon::parse($this->enrollment_date)->format("d.m.Y") . ' - ' . Carbon::parse($this->completion_deadline)->format("d.m.Y")  ;
    }


}
