<?php

namespace App\Filament\Student\Resources\LessonResource\Pages;

use App\Filament\Student\Resources\LessonResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateLesson extends CreateRecord
{
    protected static string $resource = LessonResource::class;
}
