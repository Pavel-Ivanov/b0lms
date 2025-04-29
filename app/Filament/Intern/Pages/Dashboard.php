<?php

namespace App\Filament\Intern\Pages;

use App\Filament\Intern\Widgets\AssignedCoursesWidget;
use App\Filament\Intern\Widgets\MyTestsResults;
use Filament\Pages\Page;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
//    protected static string $view = 'filament.intern.pages.dashboard';
    protected ?string $heading = 'Академия';
    protected static ?string $navigationLabel = 'Академия';

    public function getColumns(): int | array
    {
        return 1;
    }

    public function getWidgets(): array
    {
        return [
            AssignedCoursesWidget::class,
            MyTestsResults::class
        ];
    }
}
