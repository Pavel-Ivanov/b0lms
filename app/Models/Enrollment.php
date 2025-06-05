<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

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

    public function steps(): HasMany
    {
        return $this->hasMany(EnrollmentStep::class);
    }

    public function completedSteps()
    {
        return $this->steps()->where('is_completed', true);
    }

    public function hasSteps(): bool
    {
        return (bool) $this->is_steps_created;
    }

    public function enrollmentInfo(): string
    {
        return $this->user->name . ' / ' . Carbon::parse($this->enrollment_date)->format("d.m.Y") . ' - ' . Carbon::parse($this->completion_deadline)->format("d.m.Y")  ;
    }

    public function progress(): array
    {
        $stepsCount   = $this->steps->count();
        $completedCount = $this->completedSteps()->count();

        return [
            'value'      => $completedCount,
            'max'        => $stepsCount,
            'percentage' => (int) floor(($completedCount / max(1, $stepsCount)) * 100),
        ];
    }

    public function isCompleted(): bool
    {
        if (!$this->hasSteps()) {
            return false;
        }

        $stepsCount = $this->steps->count();
        $completedCount = $this->completedSteps()->count();

        return $stepsCount > 0 && $stepsCount === $completedCount;
    }

    public static function hasIncompleteEnrollment(int $courseId, int $userId): bool
    {
        return self::where('course_id', $courseId)
            ->where('user_id', $userId)
            ->whereHas('steps', function ($query) {
                $query->where('is_completed', false);
            })
            ->exists();
    }

    public function createLearningPlan(): array
    {
        if ($this->is_steps_created) {
            return [
                'success' => false,
                'message' => 'План обучения уже создан',
                'count' => 0,
                'total' => 0
            ];
        }

        $course = $this->course;
        if (!$course) {
            throw new \Exception('Enrollment must be related to a course.');
        }

        $userId = $this->user_id;
        if (!$userId) {
            throw new \Exception('Enrollment must be associated with a user.');
        }

        $stepsData = $course->getSteps();
        $steps = [];
        foreach ($stepsData as $index => $step) {
            $steps[] = [
                'enrollment_id' => $this->id,
                'course_id' => $course->id,
                'user_id' => $userId,
                'stepable_id' => $step['stepable_id'],
                'stepable_type' => $step['stepable_type'],
                'position' => $index + 1,
                'is_completed' => false,
            ];
        }

        $insertedCount = DB::table('enrollment_steps')->insertOrIgnore($steps);

        if ($insertedCount === count($steps)) {
            $this->update(['is_steps_created' => true]);

            return [
                'success' => true,
                'message' => 'План обучения создан',
                'count' => $insertedCount,
                'total' => count($steps)
            ];
        }

        // Delete all steps that were created for this enrollment
        DB::table('enrollment_steps')->where('enrollment_id', $this->id)->delete();

        return [
            'success' => false,
            'message' => 'Ошибка записи плана обучения',
            'count' => $insertedCount,
            'total' => count($steps)
        ];
    }

}
