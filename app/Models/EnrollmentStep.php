<?php

namespace App\Models;

use App\Enums\StepType;
use Illuminate\Database\Eloquent\Model;

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
//        'step_type' => StepType::class,
        'is_completed' => 'boolean',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];


}
