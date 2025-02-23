<?php

namespace App\Filament\Sadmin\Resources\CourseLevelResource\Pages;

use App\Filament\Sadmin\Resources\CourseLevelResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageCourseLevels extends ManageRecords
{
    protected static string $resource = CourseLevelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
