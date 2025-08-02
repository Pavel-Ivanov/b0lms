<?php

namespace App\Filament\Intern\Pages;

//use App\Models\Test;
use App\Models\Enrollment;
use App\Models\EnrollmentStep;
use App\Models\Lesson;
use App\Models\Quiz;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Collection;

//use Illuminate\Database\Eloquent\Model;
//use Illuminate\View\View;

/**
 * Class EnrollmentView
 *
 * Represents a Filament page for viewing and managing enrollment details.
 * This class extends the Filament Page class and provides functionality
 * for displaying enrollment steps, lessons, quizzes, and tracking progress.
 *
 * @package App\Filament\Pages\Intern
 * @extends \Filament\Pages\Page
 */
class EnrollmentView extends Page
{
    protected static string $view = 'filament.intern.pages.enrollment-view';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $slug = 'enrollments';

    protected function getListeners(): array
    {
        return [
            'refresh-enrollment-view' => 'refresh',
        ];
    }

    public Enrollment $enrollment;

    /**
     * The collection of enrollment steps.
     *
     * This property stores a Collection of EnrollmentStep models associated with the current Enrollment.
     * It represents the sequence of steps (lessons, quizzes, etc.) that a user needs to complete
     * as part of their enrollment in a course.
     *
     * @var Collection<EnrollmentStep>
     */
    public Collection $steps;

    /**
     * The ID of the active enrollment step.
     *
     * This property stores the ID of the currently active step in the enrollment process.
     * It is used to keep track of the user's progress and determine which step to display.
     *
     * @var int|null The ID of the active step, or null if no step is currently active.
     */

    public int | null $activeStepId = null;

    /**
     * The currently active enrollment step.
     *
     * This property holds the EnrollmentStep model instance that is currently active in the enrollment view.
     * It represents the step that the user is currently viewing or interacting with.
     * It is set to null when no step is currently active.
     *
     * @var EnrollmentStep|null
     */
    public ?EnrollmentStep $activeStep = null;

    /**
     * The currently active lesson.
     *
     * This property holds the Lesson model instance that is currently active in the enrollment view.
     * It is set to null when no lesson is active or when the active step is not a lesson.
     *
     * @var Lesson|null
     */
    public ?Lesson $activeLesson = null;

    /**
     * The currently active quiz.
     *
     * This property holds the Quiz model instance that is currently active in the enrollment view.
     * It is set to null when no quiz is active or when the active step is not a quiz.
     *
     * @var Quiz|null
     */
    public ?Quiz $activeQuiz = null;

    /**
     * Mounts the necessary data for the component based on the provided record and optional step.
     *
     * @param int|string $enrollment_id The ID or identifier of the Enrollment record to load.
     * @param int|null $step_id The optional step ID to be made active.
     *
     * @return void
     */
    public function mount(int|string $enrollment_id, ?int $step_id = null): void
    {
        $this->enrollment = Enrollment::findOrFail($enrollment_id);
        $this->steps = $this->enrollment->steps;
        $this->activeStepId = $step_id;
        $this->activeStep = EnrollmentStep::find($step_id);
        $this->loadStepContent($step_id);
    }

    public function getTitle(): string|Htmlable
    {
        return $this->enrollment->course->name;
    }

    /**
     * Loads the content for a specific enrollment step.
     *
     * This function retrieves the content (lesson or quiz) associated with the given step ID.
     * If no step ID is provided, it loads the content for the first step in the enrollment.
     *
     * @param int|null $stepId The ID of the enrollment step to load. If null, the first step will be loaded.
     *
     * @return void This function does not return a value, but updates the class properties:
     *              - activeLesson: Set to the Lesson object if the step is a lesson, null otherwise.
     *              - activeQuiz: Set to the Quiz object if the step is a quiz, null otherwise.
     *              - activeStepId: Set to the ID of the loaded step.
     */
    public function loadStepContent(?int $stepId): void
    {
        if ($stepId) {
            $enrollmentStep = EnrollmentStep::findOrFail($stepId);

            // Проверка доступности шага
            if (!$enrollmentStep->isEnabled()) {
                // Если шаг недоступен, перенаправляем на первый доступный шаг
                $firstEnabledStep = $this->steps->where('is_enabled', true)->sortBy('position')->first();
                if ($firstEnabledStep) {
                    $this->loadStepContent($firstEnabledStep->id);
                    return;
                }
            }

            $this->activeLesson = null;
            $this->activeQuiz = null;

            if ($enrollmentStep->stepable_type === Lesson::class) {
                $this->activeLesson = Lesson::findOrFail($enrollmentStep->stepable_id);
            } elseif ($enrollmentStep->stepable_type === Quiz::class) {
                $this->activeQuiz = Quiz::findOrFail($enrollmentStep->stepable_id);
            }
            $this->activeStepId = $stepId;
            $this->activeStep = $enrollmentStep;
        } else {
            $firstEnrollmentStep = $this->steps->where('is_enabled', true)->first();
            if ($firstEnrollmentStep) {
                $this->loadStepContent($firstEnrollmentStep->id);
                $this->activeStepId = $firstEnrollmentStep->id;
                $this->activeStep = $firstEnrollmentStep;
            }
        }
    }
    /**
     * Generates an array of navigation items for the enrollment steps.
     *
     * This function maps each enrollment step to a navigation item, including
     * information such as the step's label, URL, template, active status, and
     * completion status. It's used to build the navigation structure for the
     * enrollment view.
     *
     * @return array An array of navigation items, where each item is an associative array containing:
     *               - step: The EnrollmentStep model instance
     *               - stepModel: The related model (Lesson or Quiz) for the step
     *               - label: The display label for the step
     *               - url: The URL for the step
     *               - template: The template name for rendering the step
     *               - active: Boolean indicating if this is the currently active step
     *               - completed: Boolean indicating if the step has been completed
     */
/*    public function getNavigation(): array
    {
        return $this->steps->map(function (EnrollmentStep $enrollmentStep) {
            $url = route('filament.intern.pages.enrollments', ['enrollment_id' => $this->enrollment, 'step_id' => $enrollmentStep->id]);
//            $label = '';
//            $template = '';
            $results = null;

            if ($enrollmentStep->stepable_type === Lesson::class) {
                $lesson = Lesson::find($enrollmentStep->stepable_id);
                $label = $lesson->name ?? 'Урок';
                $template = 'filament.pages.intern.lesson-item';
            }
            elseif ($enrollmentStep->stepable_type === Quiz::class) {
                $quiz = Quiz::find($enrollmentStep->stepable_id);
                $label = $quiz->name ?? 'Тест';
                $template = 'filament.pages.intern.quiz-item';
                if ($enrollmentStep->isCompleted()) {
                    $test = Test::where('quiz_id', $quiz->id)
                        ->where('user_id', auth()->id())
                        ->latest()
                        ->first();
                    if ($test) {
                        $results = [
                            'correct' => $test->testAnswers()->where('correct', 1)->count(),
                            'incorrect' => $test->testAnswers()->where('correct', 0)->count(),
                            'total' => $quiz->questions()->count(),
                        ];
                    }

                }
            }
            else {
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
                'completed' => $enrollmentStep->isCompleted(),
                'results' => $results,
            ];
        })->toArray();
    }*/

    public function getEnrollment(): Enrollment
    {
        return $this->enrollment;
    }

    public function getActiveStep(): EnrollmentStep
    {
        return $this->activeStep;
    }

    public function getProgress()
    {
        $totalSteps = $this->steps->count();
        $completedSteps = $this->enrollment->steps()
            ->where('is_completed', true)
            ->count();

        return [
            'percent' => $totalSteps > 0 ? round(($completedSteps / $totalSteps) * 100) : 0,
            'completed' => $completedSteps,
            'total' => $totalSteps
        ];
    }


    public static function getNavigationItems(): array
    {
        return []; // Скрываем из основной навигации Filament
    }

    public static function getRoutePath(): string
    {
        return '/' . static::getSlug() . '/{enrollment_id}/step/{step_id}';
    }

    public function markLessonAsCompleted()
    {
        $activeStep = EnrollmentStep::findOrFail($this->activeStepId);
        $activeStep->update(['is_completed' => true]);

        // Находим следующий шаг и делаем его доступным
        $nextStep = $this->enrollment->steps()
            ->where('position', '>', $activeStep->position)
            ->orderBy('position')
            ->first();

        if ($nextStep) {
            $nextStep->update(['is_enabled' => true]);

            // Переходим к следующему шагу
            return redirect()->route('filament.intern.pages.enrollments', [
                'enrollment_id' => $this->enrollment->id,
                'step_id' => $nextStep->id
            ]);
        }

        $this->dispatch('enrollment-step-completed');
        $this->refresh();
    }
    public function refresh()
    {
        $this->steps = $this->enrollment->steps()->get();
        $this->loadStepContent($this->activeStepId);
    }

/*    public function render(): View
    {
        return view('filament.pages.intern.enrollment-view', [
            'navigation' => view('filament.intern.components.enrollment-navigation', [
                'enrollment' => $this->enrollment,
                'activeStepId' => $this->activeStep?->id,
            ]),
        ]);
    }*/
}
