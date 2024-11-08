<?php

namespace App\Providers\Filament;

use Filament\Pages;
use Filament\Panel;
use Filament\Widgets;
use App\Models\Tenant;
use Filament\PanelProvider;
use Filament\Pages\Dashboard;
use App\Filament\LoginBackground;
use Filament\Navigation\MenuItem;
use Filament\Support\Colors\Color;
use Filament\Navigation\NavigationItem;
use App\Filament\Resources\UserResource;
use App\Http\Middleware\ApplyTenantScopes;
use App\Http\Middleware\TenantsPermission;
use Filament\Http\Middleware\Authenticate;
use Filament\Navigation\NavigationBuilder;
use Filament\Pages\Tenancy\RegisterTenant;
use App\Filament\Pages\TenantConfiguration;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use App\Filament\Pages\Tenancy\TenantRegistration;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Swis\Filament\Backgrounds\FilamentBackgroundsPlugin;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->tenant(Tenant::class)
            // ->tenantRegistration(TenantRegistration::class)
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->discoverClusters(in: app_path('Filament/Clusters'), for: 'App\\Filament\\Clusters')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([])
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
            ->tenantMiddleware([
                TenantsPermission::class,
                \Hasnayeen\Themes\Http\Middleware\SetTheme::class
            ], isPersistent: true)
            ->authMiddleware([
                Authenticate::class,
            ])
            ->darkMode(false)

            ->plugins([
                FilamentBackgroundsPlugin::make()
                    ->imageProvider(
                        LoginBackground::make()
                            ->directory('backgrounds')
                    ),
                \Hasnayeen\Themes\ThemesPlugin::make()
                    ->canViewThemesPage(fn() => false)
                    ->registerTheme(
                        [
                            \Hasnayeen\Themes\Themes\Sunset::class,
                        ],
                        override: true,
                    )
            ])
          
            ->sidebarCollapsibleOnDesktop()
            ->globalSearch(true)
            ->globalSearchKeyBindings(['command+x', 'ctrl+k'])
            ->userMenuItems([
                MenuItem::make()
                    ->label('Tenant Configirations')
                    ->url(fn(): string => TenantConfiguration::getUrl())
                    ->icon('heroicon-o-cog-6-tooth')
                    ->visible(fn() => auth()->user()->hasRole('admin|tenant_owner')),
                // ...
            ])
            ->viteTheme('resources/css/filament/admin/theme.css');
    }
}
