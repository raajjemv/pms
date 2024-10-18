<?php

namespace App\Providers;

use Filament\Support\Assets\Js;
use Filament\Support\Assets\Css;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Filament\Support\Facades\FilamentAsset;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Filament\Support\Facades\FilamentView;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(
            LoginResponse::class,
            \App\Http\Responses\LoginResponse::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        FilamentAsset::register([
            Css::make('font-awesome-icons', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css'),
        ]);

        // FilamentAsset::register([
        //     Js::make('custom-script', ),
        // ]);

        FilamentView::registerRenderHook('panels::body.end', fn(): string => Blade::render("@vite('resources/js/flat.js')"));
    }
}
