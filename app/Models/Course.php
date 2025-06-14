<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Course extends Model implements HasMedia
{
    use InteractsWithMedia;
    protected $fillable = [
        'name',
        'announcement',
        'description',
        'duration',
        'is_published',
        'course_type_id',
        'course_level_id',
        'course_category_id',
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    public function scopePublished(Builder $query): void
    {
        $query->where('is_published', true);
    }

    public function courseType(): BelongsTo
    {
        return $this->belongsTo(CourseType::class);
    }

    public function courseLevel(): BelongsTo
    {
        return $this->belongsTo(CourseLevel::class);
    }

    public function courseCategory(): BelongsTo
    {
        return $this->belongsTo(CourseCategory::class);
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class)->orderBy('position');
    }

    public function publishedLessons(): HasMany
    {
        return $this->lessons()->published();
    }

    public function lessonsWithQizzes(): HasMany
    {
        return $this->lessons()->with('quizzes');
    }

    public function lessonsWithQuestions(): HasMany
    {
        return $this->lessons()->with('questions');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

/*    public function quizzes(): HasMany
    {
        return $this->hasMany(Quiz::class);
    }*/

    public function courseDates(): string
    {
        return Carbon::parse($this->enrollment_date)->format("d.m.Y") . ' - ' . Carbon::parse($this->completion_deadline)->format("d.m.Y")  ;
    }

    public function getSteps(): array
    {
        // Получаем только опубликованные уроки курса вместе с квизами
        $lessons = $this->publishedLessons()->with('publishedQuizzes')->get();
/*        $lessons = $this->lessons()->where('is_published', true)->with(['quizzes' => function($query) {
            $query->where('is_published', true);
        }])->get();*/

        // Создаём последовательность шагов
        $steps = [];
        foreach ($lessons as $lesson) {
            // Урок добавляется в последовательность
            $steps[] = [
                'stepable_id' => $lesson->id,
                'stepable_type' => Lesson::class,
            ];

            // Добавляем связанные с уроком квизы, только если они опубликованы
            foreach ($lesson->publishedQuizzes as $quiz) {
//                if ($quiz->is_published) {
                    $steps[] = [
                        'stepable_id' => $quiz->id,
                        'stepable_type' => Quiz::class,
                    ];
//                }
            }
        }

        return $steps;
    }


    public function progress(): array
    {
        $lessons   = $this->publishedLessons;
/*        $completed = auth()->user()->completedLessons()
            ->whereIn('lesson_id', $lessons->pluck('id'))
            ->count();*/
        $completed =1;

        return [
            'value'      => $completed,
            'max'        => $lessons->count(),
            'percentage' => (int) floor(($completed / max(1, $lessons->count())) * 100),
        ];
    }

}
