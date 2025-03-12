<?php

namespace App\Filament\Sadmin\Resources\LessonResource\Pages;

use App\Filament\Sadmin\Resources\LessonResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;

class EditLesson extends EditRecord
{
    protected static string $resource = LessonResource::class;

    public function getHeading(): string|Htmlable
    {
        return $this->record->name;
    }


    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return self::getResource()::getUrl('index');
    }

}
