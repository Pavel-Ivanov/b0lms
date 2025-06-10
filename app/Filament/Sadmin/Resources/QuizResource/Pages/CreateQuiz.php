<?php

namespace App\Filament\Sadmin\Resources\QuizResource\Pages;

use App\Filament\Sadmin\Resources\QuizResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateQuiz extends CreateRecord
{
    protected static string $resource = QuizResource::class;
    protected static bool $canCreateAnother = false;

    protected function getRedirectUrl(): string
    {
        return self::getResource()::getUrl('index');
    }

}
