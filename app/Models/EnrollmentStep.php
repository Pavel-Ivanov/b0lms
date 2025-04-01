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

public function stepableModel(): ?Model
{
    try {
        return $this->stepable_type::findOrFail($this->stepable_id);
    } catch (\Exception $e) {
        return null; // Либо бросить исключение
    }
}

}
