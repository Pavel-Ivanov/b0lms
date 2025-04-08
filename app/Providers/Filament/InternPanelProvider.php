<?php

namespace App\Providers\Filament;

use App\Filament\Intern\Widgets\AssignedCoursesWidget;
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

class InternPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('intern')
            ->path('intern')
            ->login()
            ->brandLogo('/images/logo-stovesta-small.png')
//            ->brandName('Академия StoVesta')
            ->colors([
                'primary' => Color::Gray,
            ])
            ->topNavigation()
//            ->navigation(false)
//            ->discoverResources(in: app_path('Filament/Intern/Resources'), for: 'App\\Filament\\Intern\\Resources')
//            ->discoverPages(in: app_path('Filament/Intern/Pages'), for: 'App\\Filament\\Intern\\Pages')
            ->pages([
//                Pages\Dashboard::class,
                \App\Filament\Intern\Pages\Dashboard::class,
                \App\Filament\Intern\Pages\CourseView::class,
                \App\Filament\Intern\Pages\LessonView::class,
                \App\Filament\Intern\Pages\QuizView::class,

                \App\Filament\Pages\Intern\EnrollmentView::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Intern/Widgets'), for: 'App\\Filament\\Intern\\Widgets')
            ->widgets([
//                AssignedCoursesWidget::class,
//                Widgets\AccountWidget::class,
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
            ]);
    }
}
