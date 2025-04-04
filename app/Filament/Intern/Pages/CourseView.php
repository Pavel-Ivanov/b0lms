<?php

namespace App\Filament\Intern\Pages;

use App\Models\Course;
use App\Models\Enrollment;
use Filament\Pages\Page;
use Illuminate\Database\Eloquent\Model;

class CourseView extends Page
{
    protected static string $view = 'filament.intern.pages.course-view';
    protected static ?string $title = 'Обзор курса';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $slug = 'courses';

    public Model | int | string | null $record;

    public function mount(int|string $record): void
    {
        $this->record = Course::findOrFail($record);
    }

    public static function getRoutePath(): string
    {
        return '/' . static::getSlug() . '/{record}';
    }

}
