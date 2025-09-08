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
        'last_synced_at',
    ];

    protected function casts(): array
    {
        return [
            'enrollment_date' => 'datetime',
            'completion_deadline' => 'date',
            'is_steps_created' => 'boolean',
            'last_synced_at' => 'datetime',
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

    public function progressData(): array
    {
        $stepsCount   = $this->steps->count();
        $completedCount = $this->completedSteps()->count();

        return [
            'value'      => $completedCount,
            'max'        => $stepsCount,
            'percentage' => (int) floor(($completedCount / max(1, $stepsCount)) * 100),
        ];
    }

    public function progress()
    {
        return $this->hasMany(EnrollmentStep::class);
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

    public function syncLearningPlan(bool $reindex = true, bool $autoEnable = true, bool $includeCompletedReopen = false): array
    {
        $added = 0;
        $reindexed = 0;
        $enabled = 0;

        // If enrollment is fully completed and we don't want to reopen, abort politely
        if ($this->isCompleted() && !$includeCompletedReopen) {
            $this->update(['last_synced_at' => now()]);
            return [
                'success' => true,
                'message' => 'Назначение завершено — синхронизация пропущена (ничего не изменено).',
                'added' => 0,
                'reindexed' => 0,
                'enabled' => 0,
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

        // Reference steps from the current course content
        $reference = $course->getSteps();

        // Build key => desired position from reference
        $referenceKeys = [];
        foreach ($reference as $idx => $r) {
            $key = ($r['stepable_type'] ?? '') . '|' . ($r['stepable_id'] ?? '');
            $referenceKeys[$key] = $idx + 1; // 1-based position
        }

        // Load existing steps keyBy composite key
        $existing = $this->steps()->get();
        $existingByKey = [];
        foreach ($existing as $step) {
            $k = $step->stepable_type . '|' . $step->stepable_id;
            $existingByKey[$k] = $step;
        }

        // Add missing steps
        foreach ($reference as $r) {
            $key = ($r['stepable_type'] ?? '') . '|' . ($r['stepable_id'] ?? '');
            if (!isset($existingByKey[$key])) {
                $step = new EnrollmentStep([
                    'enrollment_id' => $this->id,
                    'course_id' => $this->course_id,
                    'user_id' => $userId,
                    'stepable_id' => $r['stepable_id'],
                    'stepable_type' => $r['stepable_type'],
                    'position' => 0, // will set below
                    'is_completed' => false,
                    'is_enabled' => false,
                ]);
                $step->save();
                $existing->push($step);
                $existingByKey[$key] = $step;
                $added++;
            }
        }

        // Reindex positions according to reference order
        if ($reindex) {
            foreach ($referenceKeys as $key => $desiredPos) {
                if (isset($existingByKey[$key])) {
                    $step = $existingByKey[$key];
                    if ($step->position !== $desiredPos) {
                        $step->position = $desiredPos;
                        $step->save();
                        $reindexed++;
                    }
                }
            }
        }

        // Ensure the next actionable step is enabled
        $stepsOrdered = $this->steps()->orderBy('position')->get();
        $firstIncomplete = $stepsOrdered->firstWhere('is_completed', false);
        if ($autoEnable && $firstIncomplete) {
            if (!$firstIncomplete->is_enabled) {
                $firstIncomplete->is_enabled = true;
                $firstIncomplete->save();
                $enabled++;
            }
        }

        $this->update(['last_synced_at' => now()]);

        return [
            'success' => true,
            'message' => 'Синхронизация выполнена',
            'added' => $added,
            'reindexed' => $reindexed,
            'enabled' => $enabled,
        ];
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
                'is_enabled' => $index === 0, // Enable only the first step
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
