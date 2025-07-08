<?php

namespace App\Filament\Teacher\Resources\UserResource\Pages;

use App\Filament\Teacher\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;
}
