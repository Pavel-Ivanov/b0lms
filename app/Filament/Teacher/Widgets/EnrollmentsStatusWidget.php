<?php

namespace App\Filament\Teacher\Widgets;

use App\Models\Enrollment;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use IbrahimBougaoua\FilaProgress\Tables\Columns\ProgressBar;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class EnrollmentsStatusWidget extends BaseWidget
{
    use Tables\Concerns\InteractsWithTable;

    protected static ?string $heading = 'Назначения';

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading(function () {
                $state = data_get($this->getTableFilterState('state'), 'value') ?? 'overdue';
                return static::$heading . ' - ' . collect([
                    'incomplete' => 'Не завершенные',
                    'overdue' => 'Просроченные',
                ])->get($state);
            })
            ->query(
                Enrollment::query()
                    ->with(['course', 'user'])
            )
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Студент')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('course.name')
                    ->label('Курс')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('enrollment_date')
                    ->label('Дата назначения')
                    ->date('d.m.Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('completion_deadline')
                    ->label('Дата окончания')
                    ->date('d.m.Y')
                    ->sortable(),
                ProgressBar::make('bar')
                    ->label('Выполнено')
                    ->getStateUsing(function (Enrollment $record) {
                        $progressData = $record->progressData();
                        return [
                            'total' => $progressData['max'],
                            'progress' => $progressData['value'],
                        ];
                    }),
            ])
            ->filters([
                SelectFilter::make('state')
                    ->label('Состояние')
                    ->options([
                        'incomplete' => 'Не завершенные',
                        'overdue' => 'Просроченные',
                    ])
                    ->default('overdue')
                    ->query(function (Builder $query, array $data): Builder {
                        $today = Carbon::today();
                        $value = $data['value'] ?? 'incomplete';
                        return match ($value) {
                            'overdue' => $query
                                ->whereDate('completion_deadline', '<', $today)
                                ->where(function (Builder $q) {
                                    // Has at least one incomplete step OR steps not yet created
                                    $q->whereHas('steps', fn ($s) => $s->where('is_completed', false))
                                      ->orWhere('is_steps_created', false);
                                }),
                            'incomplete' => $query
                                // Not completed and not overdue as of today
                                ->whereDate('completion_deadline', '>=', $today)
                                ->where(function (Builder $q) {
                                    $q->whereHas('steps', fn ($s) => $s->where('is_completed', false))
                                      ->orWhere('is_steps_created', false);
                                }),
                            default => $query,
                        };
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('open')
                    ->label('Открыть')
                    ->icon('heroicon-o-arrow-right')
                    ->url(function (Enrollment $record): string {
                        // Try to lead to Teacher Enrollment view page if available
                        $pageClass = \App\Filament\Teacher\Resources\EnrollmentResource\Pages\ViewEnrollment::class;
                        if (class_exists($pageClass)) {
                            return $pageClass::getUrl([$record->id]);
                        }
                        return '#';
                    }, shouldOpenInNewTab: false),
            ])
            ->persistFiltersInSession();
    }
}
