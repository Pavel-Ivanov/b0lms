<?php

namespace App\Filament\Intern\Pages;

use App\Filament\Intern\Widgets\AssignedCoursesWidget;
use Filament\Pages\Page;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

//    protected static string $view = 'filament.intern.pages.dashboard';

    public function getColumns(): int | array
    {
        return 2;
    }

    public function getWidgets(): array
    {
        return [
//            parent::getWidgets(),
            AssignedCoursesWidget::class,
        ];

//        return parent::getWidgets();
    }
}
