<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Sadmin\Backups;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use ShuvroRoy\FilamentSpatieLaravelBackup\FilamentSpatieLaravelBackupPlugin;

class SadminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
//            ->default()
            ->id('sadmin')
            ->path('sadmin')
            ->login()
            ->colors([
                'primary' => Color::Gray,
            ])
            ->navigationGroups([
                'Академия',
                'Компания',
                'Справочники',
                'Администрирование',
            ])
        ->discoverResources(in: app_path('Filament/Sadmin/Resources'), for: 'App\\Filament\\Sadmin\\Resources')
            ->discoverPages(in: app_path('Filament/Sadmin/Pages'), for: 'App\\Filament\\Sadmin\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Sadmin/Widgets'), for: 'App\\Filament\\Sadmin\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
//                Widgets\FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->plugins([
                FilamentShieldPlugin::make(),
                FilamentSpatieLaravelBackupPlugin::make()
                    ->usingPage(Backups::class)
                    ->usingPolingInterval('60s')
                    ->timeout(120),
            ])
            ->viteTheme('resources/css/filament/sadmin/theme.css')
//            ->viteTheme('"C:\Herd\b0lms\resources\css\filament\sadmin\theme.css"')
            ;
    }
}
