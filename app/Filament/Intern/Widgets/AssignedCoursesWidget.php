<?php

namespace App\Filament\Intern\Widgets;

use App\Filament\Intern\Pages\EnrollmentView;
use App\Models\Enrollment;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use IbrahimBougaoua\FilaProgress\Tables\Columns\ProgressBar;

class AssignedCoursesWidget extends BaseWidget
{
    use Tables\Concerns\InteractsWithTable;
//    protected static ?string $heading = 'Назначенные курсы';
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading('Мои курсы')
            ->query(
                Enrollment::query()
                    ->where('user_id', auth()->user()->id)
                    ->with('course')
            )
            ->columns([
                Tables\Columns\TextColumn::make('course.name')
                    ->label('Курс'),
                Tables\Columns\TextColumn::make('enrollment_date')
                    ->label('Дата назначения')
                    ->dateTime('d-m-Y H:i'),
                Tables\Columns\TextColumn::make('completion_deadline')
                    ->label('Дата окончания')
                    ->date('d-m-Y'),
                ProgressBar::make('bar')
                    ->label('Выполнено')
                    ->getStateUsing(function (Enrollment $record) {
                        $progress = $record->progress();
                        $total = $progress['max'];
                        $progress = $progress['value'];
                        return [
                            'total' => $total,
                            'progress' => $progress,
                        ];
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('Перейти к курсу')
                    ->icon('heroicon-o-arrow-right')
                    ->url(function (Enrollment $record):string {
                        return EnrollmentView::getUrl([
                            'enrollment_id' => $record->id,
                            'step_id' => 0,
                        ]);
                    }),
            ]);
    }
}
