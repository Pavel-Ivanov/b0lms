<?php

namespace App\View\Components\Filament\Intern;

use App\Models\Enrollment;
use App\Models\EnrollmentStep;
use App\Models\Lesson;
use App\Models\Quiz;
use App\Models\Test;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class EnrollmentNavigation extends Component
{
    public Enrollment $enrollment;
    public ?int $activeStepId;

    public function __construct(Enrollment $enrollment, ?int $activeStepId = null)
    {
        $this->enrollment = $enrollment;
        $this->activeStepId = $activeStepId;
    }

    public function getNavigation(): array
    {
        return $this->enrollment->steps->map(function (EnrollmentStep $enrollmentStep) {
            $url = route('filament.intern.pages.enrollments', ['enrollment_id' => $this->enrollment->id, 'step_id' => $enrollmentStep->id]);
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
                else {
                    $results = [
                        'correct' => 0,
                        'incorrect' => 0,
                        'total' => $quiz->questions()->count(),
                    ];
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
                'url' => $enrollmentStep->isEnabled() ? $url : '#', // Используем is_enabled для определения URL
                'template' => $template,
                'active' => $this->activeStepId === $enrollmentStep->id,
                'completed' => $enrollmentStep->isCompleted(),
                'accessible' => $enrollmentStep->isEnabled(), // Добавляем флаг доступности
                'results' => $results,
            ];
        })->toArray();
    }
    public function render(): View
    {
        return view('filament.intern.components.enrollment-navigation', [
            'navigation' => $this->getNavigation(),
        ]);
    }
}
