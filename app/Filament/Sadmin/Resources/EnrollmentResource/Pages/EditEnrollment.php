<?php

namespace App\Filament\Sadmin\Resources\EnrollmentResource\Pages;

use App\Filament\Sadmin\Resources\EnrollmentResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditEnrollment extends EditRecord
{
    protected static string $resource = EnrollmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->action(function ($data, $record) {
                    if ($record->steps()->count() > 0) {
                        Notification::make()
                            ->danger()
                            ->title('Это значение используется')
                            ->body('У Назначения есть Программа обучения и оно не может быть удалено.')
                            ->send();
                        return;
                    }
                    Notification::make()
                        ->success()
                        ->title('Значение удалено')
                        ->body('Значение успешно удалено.')
                        ->send();
                    $record->delete();
                    redirect()->to(self::getResource()::getUrl('index'));
                }),

        ];
    }

    protected function getRedirectUrl(): string
    {
        return self::getResource()::getUrl('index');
    }
}
