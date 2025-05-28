<?php

namespace App\Infolists\Components;

use App\Models\Course;
use App\Models\Lesson;
use Filament\Infolists\Components\Component;
use Filament\Infolists\Components\Concerns\HasName;
use Filament\Student\Resources\CourseResource;
use Illuminate\Support\Collection;

class ListLessons extends Component
{
    use HasName;

    protected string $view = 'infolists.components.list-lessons';

    protected Course $course;
    protected Collection $lessons;
    protected Lesson |null $activeLesson = null;

    final public function __construct(string $name)
    {
        $this->name($name);
        $this->statePath($name);
    }

    public static function make(string $name): static
    {
        return app(static::class, ['name' => $name]);
    }

    public function course($course)
    {
        $this->course  = $course;
        $this->lessons = $course->lessonsWithQizzes;

        return $this;
    }

    public function activeLesson($lesson)
    {
        $this->activeLesson = $lesson;

        return $this;
    }

    public function getActiveLesson()
    {
        return $this->activeLesson;
    }

    public function isActive($lesson): bool
    {
        return $this->activeLesson?->id === $lesson->id;
    }

    public function getCourse()
    {
        return $this->course;
    }

    public function getLessons()
    {
        return $this->lessons;
    }

    public function getUrl($lesson)
    {
//        dump($this->course);
        return CourseResource::getUrl('lessons.view', [
            'parent' => $this->course,
            'record' => $lesson,
        ]);
    }

    public function getUrlQuiz($quiz)
    {
        return '#';
/*        return CourseResource::getUrl('quizzes.view', [
            'parent' => $this->course,
            'record' => $quiz,
        ]);*/
    }
}
