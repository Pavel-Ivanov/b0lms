<?php

namespace App\Filament\Sadmin\Resources\QuestionOptionResource\Pages;

use App\Filament\Sadmin\Resources\QuestionOptionResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateQuestionOption extends CreateRecord
{
    protected static string $resource = QuestionOptionResource::class;
    protected static bool $canCreateAnother = false;

    protected function getRedirectUrl(): string
    {
        return self::getResource()::getUrl('index');
    }

}
