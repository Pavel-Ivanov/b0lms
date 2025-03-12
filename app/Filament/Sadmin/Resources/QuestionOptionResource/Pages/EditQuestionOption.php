<?php

namespace App\Filament\Sadmin\Resources\QuestionOptionResource\Pages;

use App\Filament\Sadmin\Resources\QuestionOptionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditQuestionOption extends EditRecord
{
    protected static string $resource = QuestionOptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
