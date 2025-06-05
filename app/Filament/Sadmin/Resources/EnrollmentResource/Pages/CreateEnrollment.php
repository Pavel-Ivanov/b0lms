<?php

namespace App\Filament\Sadmin\Resources\EnrollmentResource\Pages;

use App\Filament\Sadmin\Resources\EnrollmentResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateEnrollment extends CreateRecord
{
    protected static string $resource = EnrollmentResource::class;
    protected static bool $canCreateAnother = false;

    protected function getRedirectUrl(): string
    {
        return self::getResource()::getUrl('index');
    }

    protected function beforeCreate(): void
    {
        $data = $this->form->getState();

        if (\App\Models\Enrollment::hasIncompleteEnrollment($data['course_id'], $data['user_id'])) {
            Notification::make()
                ->danger()
                ->title('Ошибка создания назначения')
                ->body('Уже существует незавершенное назначение этого курса для данного студента.')
                ->persistent()
                ->send();

            $this->halt();
        }
    }

    protected function afterCreate(): void
    {
        $result = $this->record->createLearningPlan();

        if ($result['success']) {
            Notification::make()
                ->success()
                ->title($result['message'])
                ->body("Создано {$result['count']} шагов.")
                ->send();
        } else {
            // Delete the enrollment if learning plan creation failed
            $this->record->delete();

            Notification::make()
                ->danger()
                ->title('Ошибка создания назначения')
                ->body($result['message'])
                ->persistent()
                ->send();

            $this->halt();
        }

    }

}
