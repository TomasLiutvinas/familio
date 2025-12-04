<?php

namespace App\Providers\Filament;

use App\Filament\Widgets\ActiveSubscriptionsCharts;
use App\Filament\Widgets\FamilioStatsOverview;
use App\Filament\Widgets\RecentPaymentsWidget;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id("admin")
            ->path("admin")
            ->login()
            ->brandLogo(fn() => view("vendor.filament.brand-logo"))
            ->favicon(asset("favicon-familio.svg"))
            ->colors([
                "primary" => [
                    50 => "254, 242, 242",
                    100 => "254, 226, 226",
                    200 => "254, 202, 202",
                    300 => "252, 165, 165",
                    400 => "220, 38, 38",
                    500 => "69, 10, 10",
                    600 => "69, 10, 10",
                    700 => "45, 7, 7",
                    800 => "45, 7, 7",
                    900 => "69, 10, 10",
                    950 => "45, 7, 7",
                ],
            ])
            ->discoverResources(
                in: app_path("Filament/Resources"),
                for: "App\Filament\Resources",
            )
            ->discoverPages(
                in: app_path("Filament/Pages"),
                for: "App\Filament\Pages",
            )
            ->pages([Dashboard::class])
            ->discoverWidgets(
                in: app_path("Filament/Widgets"),
                for: "App\Filament\Widgets",
            )
            ->widgets([
                FamilioStatsOverview::class,
                ActiveSubscriptionsCharts::class,
                RecentPaymentsWidget::class,
                AccountWidget::class,
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
            ->authMiddleware([Authenticate::class]);
    }
}
