<?php

namespace App\Filament\Sadmin\Resources\QuestionOptionResource\Pages;

use App\Filament\Sadmin\Resources\QuestionOptionResource;
use App\Models\TestAnswer;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditQuestionOption extends EditRecord
{
    protected static string $resource = QuestionOptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->action(function ($data, $record) {
                    $testAnswersCount = $record->testAnswers()->count();

                    if ($testAnswersCount > 0) {
                        Notification::make()
                            ->danger()
                            ->title('Это значение используется')
                            ->body("Эта опция используется в {$testAnswersCount} результатах тестов и не может быть удалена.")
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
                })
            ,
        ];
    }

    protected function getRedirectUrl(): string
    {
        return self::getResource()::getUrl('index');
    }

}
