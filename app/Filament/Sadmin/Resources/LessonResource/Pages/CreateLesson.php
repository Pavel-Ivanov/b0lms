<?php

namespace App\Filament\Sadmin\Resources\LessonResource\Pages;

use App\Filament\Sadmin\Resources\LessonResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateLesson extends CreateRecord
{
    protected static string $resource = LessonResource::class;
    protected static bool $canCreateAnother = false;

    protected function getRedirectUrl(): string
    {
        return self::getResource()::getUrl('index');
    }

}
