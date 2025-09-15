<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Test extends Model
{
//    use LogsActivity;

    protected $fillable = [
        'result',
        'ip_address',
        'time_spent',
        'user_id',
        'enrollment_step_id',
        'quiz_id',
        'status',
        'current_attempt',
        'attempt_number',
        'passed',
        'started_at',
        'completed_at',
        'answers',
    ];

    protected $casts = [
        'passed' => 'boolean',
        'answers' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function enrollmentStep(): BelongsTo
    {
        return $this->belongsTo(EnrollmentStep::class);
    }

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function questions(): BelongsToMany
    {
        return $this->belongsToMany(Question::class, 'test_answers', 'test_id', 'question_id');
    }

    public function testAnswers(): HasMany
    {
        return $this->hasMany(TestAnswer::class);
    }

/*    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable();
    }*/

}
