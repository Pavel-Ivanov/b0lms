<?php

namespace App\Filament\Student\Resources\QuizResource\Pages;

use App\Filament\Student\Resources\QuizResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateQuiz extends CreateRecord
{
    protected static string $resource = QuizResource::class;
}
