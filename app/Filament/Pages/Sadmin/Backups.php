<?php

namespace App\Filament\Pages\Sadmin;
use ShuvroRoy\FilamentSpatieLaravelBackup\Pages\Backups as BaseBackups;

class Backups extends BaseBackups
{
    protected static ?string $navigationIcon = 'heroicon-o-cpu-chip';

    public function getHeading(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return 'Резервные копии';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Администрирование';
    }
}
