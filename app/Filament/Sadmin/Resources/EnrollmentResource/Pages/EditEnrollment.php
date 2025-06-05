<?php

namespace App\Filament\Sadmin\Resources\EnrollmentResource\Pages;

use App\Filament\Sadmin\Resources\EnrollmentResource;
use Filament\Actions;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\HtmlString;

class EditEnrollment extends EditRecord
{
    protected static string $resource = EnrollmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->requiresConfirmation()
                ->modalHeading('Удаление назначения')
                ->modalDescription(function ($record) {
                    $hasSteps = $record->steps()->count() > 0;
                    $hasTestResults = false;

                    // Check if any enrollment steps have test results
                    foreach ($record->steps as $step) {
                        if ($step->tests()->count() > 0) {
                            $hasTestResults = true;
                            break;
                        }
                    }

                    $message = '';
                    if ($hasSteps && $hasTestResults) {
                        $message = 'У Назначения есть Программа обучения и Результаты Тестов. Вы уверены, что хотите удалить их вместе с Назначением?';
                    } elseif ($hasSteps) {
                        $message = 'У Назначения есть Программа обучения. Вы уверены, что хотите удалить её вместе с Назначением?';
                    } elseif ($hasTestResults) {
                        $message = 'У Назначения есть Результаты Тестов. Вы уверены, что хотите удалить их вместе с Назначением?';
                    } else {
                        $message = 'Вы уверены, что хотите удалить это назначение? Это действие нельзя отменить.';
                    }

                    return new HtmlString('<div style="font-size: 1.2rem; font-weight: bold; color: red;">' . $message . '</div>');
                })
                ->modalSubmitActionLabel('Да, удалить')
                ->modalCancelActionLabel('Отмена')
                ->action(function ($record) {
                    $recordId = $record->id;
                    $enrollment = \App\Models\Enrollment::find($recordId);
                    if ($enrollment) {
                        $enrollment->delete();
                    }

                    Notification::make()
                        ->success()
                        ->title('Назначение удалено')
                        ->body('Назначение успешно удалено.')
                        ->send();

                    return redirect(self::getResource()::getUrl('index'));
                }),

        ];
    }

    protected function getRedirectUrl(): string
    {
        return self::getResource()::getUrl('index');
    }

}
