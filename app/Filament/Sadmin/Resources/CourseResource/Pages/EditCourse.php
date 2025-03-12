<?php

namespace App\Filament\Sadmin\Resources\CourseResource\Pages;

use App\Filament\Sadmin\Resources\CourseResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;

class EditCourse extends EditRecord
{
    protected static string $resource = CourseResource::class;

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
