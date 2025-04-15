<?php

namespace App\Filament\Sadmin\Resources\EnrollmentStepResource\Pages;

use App\Filament\Sadmin\Resources\EnrollmentStepResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEnrollmentStep extends EditRecord
{
    protected static string $resource = EnrollmentStepResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
