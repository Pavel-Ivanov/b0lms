<?php

namespace App\Filament\Intern\Pages;

use App\Models\Enrollment;
use App\Models\Lesson;
use Filament\Pages\Page;
use Illuminate\Database\Eloquent\Model;

class LessonView extends Page
{
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $title = '';
    protected static string $view = 'filament.intern.pages.lesson-view';
    protected static ?string $slug = 'lessons';

    public Model | int | string | null $record;
    public Enrollment $enrollment;

    public function mount(int|string $record)
    {
        $this->record = Lesson::findOrFail($record);
        $this->enrollment = Enrollment::where('course_id', $this->record->course->id)
            ->where('user_id', auth()->id())
            ->with('steps')
            ->firstOrFail();
    }

    public static function getRoutePath(): string
    {
        return '/' . static::getSlug() . '/{record}';
    }

}
