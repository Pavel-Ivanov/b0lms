<?php

namespace App\Filament\Sadmin\Resources\EnrollmentResource\Pages;

use App\Filament\Sadmin\Resources\EnrollmentResource;
use Filament\Actions;
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
                        ->body(trim($msg . "\nДобавлено: {$result['added']}, Переиндексировано: {$result['reindexed']}, Открыто: {$result['enabled']}"))
                        ->success()
                        ->send();
                    $this->record->refresh();
                }),
            Actions\Action::make('syncPlanReopen')
                ->label('Дообучить (с reopen)')
                ->color('warning')
                ->requiresConfirmation()
                ->action(function () {
                    $result = $this->record->syncLearningPlan(true, true, true);
                    $msg = ($result['message'] ?? '');
                    \Filament\Notifications\Notification::make()
                        ->title('Дообучение выполнено')
                        ->body(trim($msg . "\nДобавлено: {$result['added']}, Переиндексировано: {$result['reindexed']}, Открыто: {$result['enabled']}"))
                        ->success()
                        ->send();
                    $this->record->refresh();
                }),
        ];
    }
}
