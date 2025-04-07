<?php

namespace App\Livewire;

use App\Filament\Intern\Pages\LessonView;
use App\Filament\Intern\Pages\QuizView;
use App\Filament\Student\Resources\CourseResource;
use App\Models\Enrollment;
use App\Models\EnrollmentStep;
use App\Models\Lesson;
use App\Models\Quiz;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Livewire\Component;

class CourseNavigation extends Component
{
    public Enrollment $enrollment;
    public Collection $steps;
    public ?int $activeStepId = null;

    protected static string $view = 'livewire.course-navigation';


    public function mount(Enrollment $enrollment)
    {
        $this->enrollment = $enrollment;
        $this->steps = $this->enrollment->steps()->orderBy('position')->get();
//        $this->activeStepId = $activeStep;
    }

    public function setActiveStepId(int $stepId)
    {
        $this->activeStepId = $stepId;
    }

    public function getActiveStepId()
    {
        return $this->activeStepId;
    }

    public function isStepActive(int $stepId):bool
    {
        return $this->activeStepId === $stepId;
    }

    public function getUrl(Model $step): string
    {
        if ($step instanceof Lesson) {
            return LessonView::getUrl(['record' => $step->id]);
        }
        if ($step instanceof Quiz) {
            return QuizView::getUrl(['record' => $step->id]);
        }
        return '#';
    }

    public function render()
    {
        return view('livewire.course-navigation');
    }
}
