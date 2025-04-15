<?php

namespace App\Filament\Sadmin\Resources\EnrollmentStepResource\Pages;

use App\Filament\Sadmin\Resources\EnrollmentStepResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEnrollmentSteps extends ListRecords
{
    protected static string $resource = EnrollmentStepResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
