<?php

namespace App\Filament\Sadmin\Resources\CompanyDepartmentResource\Pages;

use App\Filament\Sadmin\Resources\CompanyDepartmentResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageCompanyDepartments extends ManageRecords
{
    protected static string $resource = CompanyDepartmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
