<?php

namespace App\Filament\Student\Resources\QuizResource\Pages;

use App\Filament\Student\Resources\QuizResource;
use App\Infolists\Components\ListSteps;
use App\Models\Enrollment;
use App\Models\Lesson;
use Filament\Actions;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\TextEntry\TextEntrySize;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewQuiz extends ViewRecord
{
    protected static string $resource = QuizResource::class;

/*    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }*/

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->record($this->getRecord())
            ->schema([
                Grid::make()
                    ->columns(1)
                    ->schema([
                        TextEntry::make('name')
                            ->hiddenLabel()
                        ,
                    ])
                    ->columnSpan(2),
                Grid::make()
                    ->columns(1)
                    ->schema([
                        ListSteps::make('Шаги')
                            ->enrollment(Enrollment::where('course_id', $this->getRecord()->lesson->course->id)->where('user_id', auth()->id())->first()),
                    ])
                    ->columnSpan(1),
            ])
            ->columns(3);
    }
}
