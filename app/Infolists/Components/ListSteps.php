<?php

namespace App\Infolists\Components;

use App\Models\Enrollment;
use App\Models\EnrollmentStep;
use App\Models\Lesson;
use App\Models\Quiz;
use Filament\Infolists\Components\Component;
use Filament\Infolists\Components\Concerns\HasName;
use Filament\Student\Resources\CourseResource;
use Illuminate\Support\Collection;

class ListSteps extends Component
{
    use HasName;

    protected string $view = 'infolists.components.list-steps';

    protected Enrollment $enrollment;
    protected Collection $steps;
    protected EnrollmentStep|null $activeStep = null;

    final public function __construct(string $name)
    {
        $this->name($name);
        $this->statePath($name);
    }

    public static function make(string $name): static
    {
        return app(static::class, ['name' => $name]);
    }

    public function enrollment(Enrollment $enrollment)
    {
        $this->enrollment = $enrollment;
        $this->steps = $enrollment->steps()->orderBy('position')->get();

        return $this;
    }

    public function activeStep($step)
    {
        $this->activeStep = $step;

        return $this;
    }

    public function getActiveStep()
    {
        return $this->activeStep;
    }

    public function isActive($step): bool
    {
        return $this->activeStep?->id === $step->id;
    }

    public function getEnrollment()
    {
        return $this->enrollment;
    }

    public function getSteps()
    {
        return $this->steps;
    }

public function getUrl($step)
{
    return match (get_class($step)) {
        Lesson::class => CourseResource::getUrl('lessons.view', [
            'parent' => $this->enrollment->course,
            'record' => $step,
        ]),
        Quiz::class => CourseResource::getUrl('quizzes.view', [
            'parent' => $this->enrollment->course,
            'record' => $step,
        ]),
        default => '#',
    };
}

    public function getStepName($step)
    {
        return $step->stepableModel()->name ?? '';
    }

    public function getStepType($step)
    {
        return class_basename($step->stepable_type);
    }
}
