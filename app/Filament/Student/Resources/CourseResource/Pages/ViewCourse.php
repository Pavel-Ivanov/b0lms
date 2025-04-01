<?php

namespace App\Filament\Student\Resources\CourseResource\Pages;

use App\Filament\Student\Resources\CourseResource;
use App\Infolists\Components\CourseProgress;
use App\Infolists\Components\ListLessons;
use App\Infolists\Components\ListSteps;
use App\Models\Course;
use App\Models\Enrollment;
use Filament\Actions;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\TextEntry\TextEntrySize;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ViewCourse extends ViewRecord
{
    protected static string $resource = CourseResource::class;

    public function getTitle(): string|Htmlable
    {
        return $this->getRecord()->name;
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->record($this->getRecord())
            ->schema([
                Grid::make()
                    ->columns(1)
                    ->schema([
                        TextEntry::make('description')
                            ->hiddenLabel()
                            ->html()
                            ->size(TextEntrySize::Medium),
                    ])
                    ->columnSpan(2),
                Grid::make()
                    ->columns(1)
                    ->schema([
/*                        CourseProgress::make()
                            ->course($this->getRecord()->course),*/
/*                        ListLessons::make('Уроки')
                            ->course($this->getRecord()),*/
                        ListSteps::make('Шаги')
                            ->enrollment(Enrollment::where('course_id', $this->getRecord()->id)->where('user_id', auth()->id())->first()),
/*                        RepeatableEntry::make('lessons')
                            ->schema([
                                TextEntry::make('name')
                                ->hiddenLabel(),
                            ])*/
                    ])
                    ->columnSpan(1),
            ])
            ->columns(3);
    }


}
