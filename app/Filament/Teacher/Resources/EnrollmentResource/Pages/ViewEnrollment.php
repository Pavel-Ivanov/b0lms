<?php

namespace App\Filament\Teacher\Resources\EnrollmentResource\Pages;

use App\Filament\Teacher\Resources\EnrollmentResource;
use App\Models\EnrollmentStep;
use App\Models\Lesson;
use App\Models\Quiz;
use Filament\Actions;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Components\Tabs\Tab;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewEnrollment extends ViewRecord
{
    protected static string $resource = EnrollmentResource::class;

    public function getTitle(): string
    {
        return $this->record->course->name . ' - ' . $this->record->enrollmentInfo();
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Tabs::make('Tabs')
                    ->tabs([
                        Tab::make('Основная информация')
                            ->schema([
                                TextEntry::make('course.name')
                                    ->label('Курс')
                                    ->inlineLabel(),
                                TextEntry::make('user.name')
                                    ->label('Студент')
                                    ->inlineLabel(),
                                TextEntry::make('enrollment_date')
                                    ->label('Дата назначения')
                                    ->date('d.m.Y')
                                    ->inlineLabel(),
                                TextEntry::make('completion_deadline')
                                    ->label('Дата окончания обучения')
                                    ->date('d.m.Y')
                                    ->inlineLabel(),
                            ]),
                        Tab::make('Результат обучения')
                            ->schema([
                                RepeatableEntry::make('steps')
                                    ->hiddenLabel()
                                    ->schema([
                                        Section::make('step')
                                            ->heading(function (EnrollmentStep $record) {
                                                $icon = match ($record->stepable_type) {
                                                    Lesson::class => 'heroicon-o-book-open',
                                                    Quiz::class => 'heroicon-o-clipboard-document-check',
                                                    default => 'heroicon-o-book-open',
                                                };

                                                $type = match ($record->stepable_type) {
                                                    Lesson::class => 'Урок',
                                                    Quiz::class => 'Тест',
                                                    default => 'Неизвестно',
                                                };

                                                $color = $record->is_completed ? 'text-success-600' : 'text-danger-600';

                                                $html = view('filament.teacher.components.enrollment-step-heading', [], [
                                                    'icon' => $icon,
                                                    'color' => $color,
                                                    'text' => $record->stepableModel()->name,
                                                    'type' => $type,
                                                ])->render();

                                                return new \Illuminate\Support\HtmlString($html);
                                            })
                                            ->schema([
                                                TextEntry::make('is_completed')
                                                    ->label('Статус:')
                                                    ->inlineLabel()
                                                    ->formatStateUsing(function ($state) {
                                                        return $state === true ? 'Завершен' : 'Не завершен';
                                                    }),
/*                                                TextEntry::make('started_at')
                                                    ->label('Начат:')
                                                    ->date('d.m.Y H:i')
                                                    ->inlineLabel(),*/
                                                TextEntry::make('completed_at')
                                                    ->label('Завершен:')
                                                    ->date('d.m.Y H:i')
                                                    ->inlineLabel(),
/*                                                TextEntry::make('stepable_type')
                                                    ->label('Тип:')
                                                    ->inlineLabel()
                                                    ->formatStateUsing(function ($state) {
                                                        return match ($state) {
                                                            Lesson::class => 'Урок',
                                                            Quiz::class => 'Тест',
                                                            default => 'Неизвестно',
                                                        };
                                                    }),*/
                                                // Отображение информации о тестах, если шаг является тестом
                                                RepeatableEntry::make('tests')
                                                    ->label('Попытки прохождения теста:')
                                                    ->visible(fn (EnrollmentStep $record) => $record->stepable_type === Quiz::class)
                                                    ->schema([
                                                        TextEntry::make('attempt_number')
                                                            ->label('Номер попытки:')
                                                            ->inlineLabel(),
                                                        TextEntry::make('result')
                                                            ->label('Результат:')
                                                            ->inlineLabel(),
                                                        TextEntry::make('passed')
                                                            ->label('Пройден:')
                                                            ->inlineLabel()
                                                            ->formatStateUsing(function ($state) {
                                                                return $state === true ? 'Да' : 'Нет';
                                                            }),
                                                        TextEntry::make('started_at')
                                                            ->label('Начат:')
                                                            ->date('d.m.Y H:i')
                                                            ->inlineLabel(),
                                                        TextEntry::make('completed_at')
                                                            ->label('Завершен:')
                                                            ->date('d.m.Y H:i')
                                                            ->inlineLabel(),
                                                        TextEntry::make('time_spent')
                                                            ->label('Затраченное время (сек):')
                                                            ->inlineLabel(),
                                                    ])
                                                    ->contained(false),
                                            ]),
                                    ])
                                    ->contained(false),
                            ]),
                    ])
                    ->persistTab()
                    ->columnSpan('full')
                    ->activeTab(1),
            ]);
    }
}
