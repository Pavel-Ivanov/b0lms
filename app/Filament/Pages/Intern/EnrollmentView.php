<?php

namespace App\Filament\Pages\Intern;

use Filament\Pages\Page;
use App\Models\Enrollment;
use App\Models\EnrollmentStep;
use App\Models\Lesson;
use App\Models\Quiz;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class EnrollmentView extends Page
{
    protected static string $view = 'filament.pages.intern.enrollment-view';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $slug = 'enrollments';


    public Enrollment | int | string | null $record;

    public Collection $steps;
//    public Enrollment $enrollment;
    public int | null $activeStepId = null;
    public ?Lesson $activeLesson = null;
    public ?Quiz $activeQuiz = null;

//    protected static ?string $title = '';

    public function mount(int|string $record, ?int $step = null): void
    {
        $this->record = Enrollment::findOrFail($record);
        $this->steps = $this->record->steps;
        $this->activeStepId = $step;
        $this->loadStepContent($step);
    }

    public function getTitle(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return $this->record->course->name;
    }

    public function loadStepContent(?int $stepId): void
    {
        if ($stepId) {
            $enrollmentStep = EnrollmentStep::findOrFail($stepId);
            $this->activeLesson = null;
            $this->activeQuiz = null;

            if ($enrollmentStep->stepable_type === Lesson::class) {
                $this->activeLesson = Lesson::findOrFail($enrollmentStep->stepable_id);
            } elseif ($enrollmentStep->stepable_type === Quiz::class) {
                $this->activeQuiz = Quiz::findOrFail($enrollmentStep->stepable_id);
            }
            $this->activeStepId = $stepId;
        } else {
            $firstEnrollmentStep = $this->steps->first();
            if ($firstEnrollmentStep) {
                $this->loadStepContent($firstEnrollmentStep->id);
            }
        }
    }

    public function getNavigation(): array
    {
        return $this->steps->map(function (EnrollmentStep $enrollmentStep) {
            $url = route('filament.intern.pages.enrollments', ['record' => $this->record, 'step' => $enrollmentStep->id]);
            $label = '';
            $icon = '';

            if ($enrollmentStep->stepable_type === Lesson::class) {
                $lesson = Lesson::find($enrollmentStep->stepable_id);
                $label = $lesson->name ?? 'Урок';
                $template = 'filament.pages.intern.lesson-item';
            } elseif ($enrollmentStep->stepable_type === Quiz::class) {
                $quiz = Quiz::find($enrollmentStep->stepable_id);
                $label = $quiz->name ?? 'Тест';
                $template = 'filament.pages.intern.quiz-item';
            } else {
                $label = 'Шаг ' . $enrollmentStep->position;
                $template = '';
            }

            return [
                'step' => $enrollmentStep,
                'stepModel' => $enrollmentStep->stepableModel(),
                'label' => $label,
                'url' => $url,
                'template' => $template,
                'active' => $this->activeStepId === $enrollmentStep->id,
            ];
        })->toArray();
    }

    public function getEnrollment(): Enrollment
    {
        return $this->record;
    }

    public static function getNavigationItems(): array
    {
        return []; // Скрываем из основной навигации Filament
    }

    public static function getRoutePath(): string
    {
        return '/' . static::getSlug() . '/{record}/step/{step}';
    }

    public function markLessonAsCompleted()
    {
        $activeStep = EnrollmentStep::findOrFail($this->activeStepId);
        $activeStep->update(['is_completed' => true]);

        $this->dispatch('enrollment-step-completed');
        $this->refresh();
    }

    public function refresh()
    {
        $this->steps = $this->record->steps()->get();
        $this->loadStepContent($this->activeStepId);
    }
}
