<?php

namespace App\Filament\Sadmin\Resources\EnrollmentResource\Pages;

use App\Filament\Sadmin\Resources\EnrollmentResource;
use App\Models\EnrollmentStep;
use Filament\Actions;
use Filament\Infolists\Components\RepeatableEntry;
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

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('syncPlan')
                ->label('Синхронизировать план')
                ->color('primary')
                ->requiresConfirmation()
                ->action(function () {
                    $result = $this->record->syncLearningPlan();
                    $msg = ($result['message'] ?? '');
                    \Filament\Notifications\Notification::make()
                        ->title('Синхронизация плана')
                        ->body(trim($msg . "\nДобавлено: {$result['added']}, Переиндексировано: {$result['reindexed']}, Открыто: {$result['enabled']}" . "\nПримечание: в план включены только опубликованные уроки и тесты на момент синхронизации."))
                        ->success()
                        ->send();
                    $this->record->refresh();
                }),
        ];
    }

    public function infoList(Infolist $infolist): Infolist
    {
        return $infolist

            ->schema([
                \Filament\Infolists\Components\Tabs::make('Tabs')
                    ->tabs([
                        \Filament\Infolists\Components\Tabs\Tab::make('Основная информация')
                            ->schema([
                                TextEntry::make('course.name')
                                    ->label('Курс')
                                    ->inlineLabel(),
                                TextEntry::make('user.name')
                                    ->label('Студент')
                                    ->inlineLabel(),
                                TextEntry::make('enrollment_date')
                                    ->label('Дата назначения')
                                    ->inlineLabel(),
                                TextEntry::make('completion_deadline')
                                    ->label('Дата окончания обучения')
                                    ->inlineLabel(),

                            ]),
                        \Filament\Infolists\Components\Tabs\Tab::make('План обучения')
                            ->schema([
                                RepeatableEntry::make('steps')
                                    ->hiddenLabel()
                                    ->schema([
                                        \Filament\Infolists\Components\Section::make('step')
                                            ->heading(function (EnrollmentStep $record) {
                                                return $record->stepableModel()->name;
                                            })
                                            ->schema([
                                                TextEntry::make('is_completed')
                                                    ->label('Статус:')
                                                    ->inlineLabel()
//                                                    ->hiddenLabel()
                                                    ->formatStateUsing(function ($state) {
                                                        return $state === true ? 'Завершен' : 'Не завершен';
                                                    }),
                                                TextEntry::make('started_at')
                                                    ->label('Начат:')
                                                    ->inlineLabel(),
                                                TextEntry::make('completed_at')
                                                    ->label('Завершен:')
                                                    ->inlineLabel(),

                                            ]),
                                    ])
                                    ->contained(false),

                            ]),
                    ])
                    ->persistTab()
                    ->columnSpan('full')
                    ->activeTab(1),

                /*                \Filament\Infolists\Components\Section::make('Основная информация')
                                    ->schema([
                                    ]),
                            \Filament\Infolists\Components\Section::make('План обучения')
                                ->schema([
                                ])
                            ->columns(),*/
            ]);
    }

}
