<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Sadmin\Backups;
use App\Filament\Sadmin\Resources\LessonResource;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use CharrafiMed\GlobalSearchModal\GlobalSearchModalPlugin;
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
use Rmsramos\Activitylog\ActivitylogPlugin;
use ShuvroRoy\FilamentSpatieLaravelBackup\FilamentSpatieLaravelBackupPlugin;
use BezhanSalleh\PanelSwitch\PanelSwitch;

class SadminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        PanelSwitch::configureUsing(function (PanelSwitch $panelSwitch) {
            $panelSwitch
                ->modalHeading('Переключатель Панелей')
                ->modalWidth('sm')
                ->labels([
                    'sadmin' => 'Панель Администратора',
                    'teacher' => 'Панель Учителя',
                    'intern' => 'Панель Студента',
                ])
                ->icons([
                    'sadmin' => 'heroicon-o-cog',
                    'teacher' => 'heroicon-o-academic-cap',
                    'intern' => 'heroicon-o-user',
                ])
                ->iconSize(12)
                ->visible(fn (): bool => auth()->user()?->hasAnyRole([
                    'Superadmin',
                    'Администратор',
                ]))
                ->panels(['sadmin', 'teacher', 'intern'])
            ;
        });

        return $panel
//            ->default()
            ->id('sadmin')
            ->path('sadmin')
            ->login()
            ->brandLogo('/images/logo-stovesta-small.png')
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
/*                GlobalSearchModalPlugin::make()
//                    ->scopes(LessonResource::class)
                    ->highlightQueryStyles([
                        'background-color' => 'yellow',
                        'font-weight' => 'bold',
                    ]),*/
                ActivitylogPlugin::make()
                    ->navigationGroup('Администрирование')
                    ->authorize(
                        fn () => auth()->user()->hasRole(['Superadmin'])
                    )
                    ->translateLogKey(fn($label) => __("events.".$label)),
            ])
//            ->viteTheme('resources/css/filament/sadmin/theme.css')
//            ->viteTheme('"C:\Herd\b0lms\resources\css\filament\sadmin\theme.css"')
            ;
    }
}
