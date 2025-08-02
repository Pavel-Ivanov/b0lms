<?php

namespace App\Filament\Teacher\Resources\UserResource\Pages;

use App\Filament\Teacher\Resources\UserResource;
use App\Models\Enrollment;
use App\Models\EnrollmentStep;
use App\Models\Lesson;
use App\Models\Quiz;
use App\Models\Test;
use Filament\Actions;
use Filament\Infolists;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Components\Tabs\Tab;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\HtmlString;

class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Tabs::make('Tabs')
                    ->tabs([
                        Tab::make('Основная информация')
                            ->schema([
                                TextEntry::make('name')
                                    ->label('Имя')
                                    ->inlineLabel(),
                                TextEntry::make('email')
                                    ->label('Email')
                                    ->inlineLabel(),
                                TextEntry::make('companyDepartment.name')
                                    ->label('Подразделение')
                                    ->inlineLabel(),
                                TextEntry::make('companyPosition.name')
                                    ->label('Должность')
                                    ->inlineLabel(),
                            ]),
                        Tab::make('Результаты обучения')
                            ->schema([
                                Infolists\Components\RepeatableEntry::make('enrollments')
                                    ->hiddenLabel()
                                    ->schema([
                                        Section::make('step')
                                            ->heading(function (Enrollment $record) {
                                                $progress = $record->progressData();
                                                $progressText = "{$progress['percentage']}% ({$progress['value']}/{$progress['max']})";

                                                $enrollmentDate = $record->enrollment_date->format('d.m.Y');
                                                $completionDate = $record->completion_deadline->format('d.m.Y');

                                                $html = view('filament.teacher.components.enrollment-step-heading', [], [
                                                    'icon' => 'heroicon-o-academic-cap',
                                                    'color' => 'text-primary-600',
                                                    'courseName' => $record->course->name,
                                                    'parameters' => "Назначен: {$enrollmentDate} | Окончание: {$completionDate} | Прогресс: {$progressText}",
                                                    'type' => 'Курс',
                                                ])->render();

                                                return new HtmlString($html);
                                            })
                                            ->collapsible()
                                            ->collapsed()
                                            ->schema([

                                                // Отображение шагов курса
                                                Infolists\Components\RepeatableEntry::make('steps')
//                                                    ->label('Шаги курса:')
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

                                                                return new HtmlString($html);
                                                            })
                                                            ->collapsible()
                                                            ->collapsed()
                                                            ->schema([
/*                                                                TextEntry::make('is_completed')
                                                                    ->label('Статус:')
                                                                    ->inlineLabel()
                                                                    ->formatStateUsing(function ($state) {
                                                                        return $state === true ? 'Завершен' : 'Не завершен';
                                                                    }),*/
                                                                TextEntry::make('completed_at')
                                                                    ->label('Завершен:')
                                                                    ->date('d.m.Y H:i')
                                                                    ->inlineLabel(),

                                                                // Отображение информации о тестах, если шаг является тестом
                                                                Infolists\Components\RepeatableEntry::make('tests')
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
                                    ->contained(false)
                                ,
                            ]),
                    ])
                    ->persistTab()
                    ->columnSpan('full')
                    ->activeTab(1),
            ]);
    }
}
