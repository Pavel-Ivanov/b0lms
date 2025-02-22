<?php

namespace App\Filament\Sadmin\Resources\CompanyPositionResource\Pages;

use App\Filament\Sadmin\Resources\CompanyPositionResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageCompanyPositions extends ManageRecords
{
    protected static string $resource = CompanyPositionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
