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
                    \Filament\Notifications\Notification::make()
                        ->title('Синхронизация плана')
                        ->body("Добавлено: {$result['added']}, Переиндексировано: {$result['reindexed']}, Открыто: {$result['enabled']}")
                        ->success()
                        ->send();
                    $this->record->refresh();
                }),
        ];
    }
}
