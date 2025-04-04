<?php

namespace App\Filament\Intern\Widgets;

use App\Filament\Intern\Pages\CourseView;
use App\Models\Enrollment;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class AssignedCoursesWidget extends BaseWidget
{
    use Tables\Concerns\InteractsWithTable;
    protected static ?string $heading = 'Назначенные курсы';
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Enrollment::query()
                    ->where('user_id', auth()->user()->id)
                    ->with('course')
            )
            ->columns([
                Tables\Columns\TextColumn::make('course.name'),
                Tables\Columns\TextColumn::make('enrollment_date')
                    ->label('Дата назначения')
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('Перейти к курсу')
                    ->icon('heroicon-o-arrow-right')
                    ->url(function (Enrollment $record):string {
                        return CourseView::getUrl(['record' => $record->course->id]);
                    }),
            ]);
    }
}
