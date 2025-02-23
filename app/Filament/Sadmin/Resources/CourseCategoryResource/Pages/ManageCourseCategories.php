<?php

namespace App\Filament\Sadmin\Resources\CourseCategoryResource\Pages;

use App\Filament\Sadmin\Resources\CourseCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageCourseCategories extends ManageRecords
{
    protected static string $resource = CourseCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
