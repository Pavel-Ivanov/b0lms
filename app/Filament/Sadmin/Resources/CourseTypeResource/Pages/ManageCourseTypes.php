<?php

namespace App\Filament\Sadmin\Resources\CourseTypeResource\Pages;

use App\Filament\Sadmin\Resources\CourseTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageCourseTypes extends ManageRecords
{
    protected static string $resource = CourseTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
